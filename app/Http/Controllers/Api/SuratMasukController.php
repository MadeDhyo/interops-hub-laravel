<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;
class SuratMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratMasuk::query();

        // 1. Smart Search / Fuzzy Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                  ->orWhere('dari', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        // 2. Date Filtering
        if ($startDate = $request->input('start_date')) {
            $query->where('tanggal_masuk', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->where('tanggal_masuk', '<=', $endDate);
        }

        // 3. Execution with Pagination
        $limit = $request->input('limit', 10);
        $paginatedData = $query->orderBy('id', 'desc')->paginate($limit);

        // 4. Global Stats for Charts (Raw counts)
        $stats = [
            'total'   => SuratMasuk::count(),
            'pending' => SuratMasuk::where('status', 'pending')->count(),
            'sla'     => SuratMasuk::where('status', 'pending')
                            ->where('tanggal_masuk', '<=', Carbon::now()->subDays(3))
                            ->count()
        ];

        return response()->json([
            'status' => 200,
            // KUNCI SAKLEK: Pake toArray()['data'] biar field flat no_dispo dll gak dibuang sama Laravel
            'data' => $paginatedData->toArray()['data'], 
            'stats' => $stats,
            'pagination' => [
                'page' => $paginatedData->currentPage(),
                'total_pages' => $paginatedData->lastPage()
            ]
        ], 200);
    }

    public function create(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('akses-admin');

        // File Upload Handling
        $fileName = null;
        if ($request->hasFile('file_pdf') && $request->file('file_pdf')->isValid()) {
            $file = $request->file('file_pdf');
            // PERBAIKAN CODE (Ganti baris 64 jadi ini):
            $fileName = time() . '_' . $file->hashName();
            // Moves file straight to public/uploads
            $file->move(public_path('uploads'), $fileName); 
        }

        $surat = SuratMasuk::create([
            'kepada'        => $request->input('kepada'),
            'dari'          => $request->input('dari'),
            'perihal'       => $request->input('perihal'),
            'tanggal_masuk' => $request->input('tanggal_masuk'),
            'no_surat'      => $request->input('no_surat'),
            'no_dispo'      => null,
            'file_pdf'      => $fileName,
            'status'        => 'pending'
        ]);

        // Audit Trail Log
        ActivityLog::create([
            'aksi'    => 'Input Surat Masuk',
            'rincian' => "Operator menginput surat nomor {$surat->no_surat} dari {$surat->dari}"
        ]);

        return response()->json(['status' => 201, 'message' => 'Surat masuk berhasil disimpan'], 201);
    }

    public function updateDisposisi(Request $request, $id)
    {
        // KUNCI UTAMA: Hanya pimpinan yang boleh mengeksekusi disposisi
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'pimpinan') {
            return response()->json(['status' => 403, 'message' => 'Aksi Ditolak! Hanya pimpinan yang dapat memberikan instruksi disposisi.'], 403);
        }

        $request->validate([
            'no_dispo' => 'required|string|max:100',
            'disposisi_kabag' => 'required|string',
            'disposisi_kasubag' => 'nullable|string',
        ]);

        $surat = SuratMasuk::find($id);
        
        if (!$surat) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan'], 404);
        }

        $surat->update([
            'no_dispo'          => $request->input('no_dispo'),
            'disposisi_kabag'   => $request->input('disposisi_kabag'),
            'disposisi_kasubag' => $request->input('disposisi_kasubag'),
            'status'            => 'disposisi'
        ]);

        // Audit Trail Log
        \App\Models\ActivityLog::create([
            'aksi'    => 'Pemberian Disposisi',
            'rincian' => "Pimpinan (" . \Illuminate\Support\Facades\Auth::user()->nama_lengkap . ") memberikan instruksi disposisi pada surat nomor {$surat->no_surat}"
        ]);

        // ==========================================
        // WHATSAPP GATEWAY (NOTIFIKASI FONNTE)
        // ==========================================
        $pesanWA = "📩 *NOTIFIKASI DISPOSISI BARU* 📩\n\n"
                 . "Terdapat instruksi baru dari Pimpinan untuk segera ditindaklanjuti.\n\n"
                 . "📌 *No Surat:* " . $surat->no_surat . "\n"
                 . "🏢 *Dari:* " . $surat->dari . "\n"
                 . "📝 *Perihal:* " . $surat->perihal . "\n\n"
                 . "🔸 *Instruksi Kabag:* " . $surat->disposisi_kabag . "\n"
                 . "🔸 *Instruksi Kasubag:* " . ($surat->disposisi_kasubag ?: '-') . "\n\n"
                 . "Silakan cek sistem InterOps-Hub untuk menindaklanjuti berkas.";

        try {
            Http::withHeaders([
                'Authorization' => '2cSbEX1edJb2dWz59iQn' // API Key Fonnte lu
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target'  => '081219408823', // Nomor HP Staf lu
                'message' => $pesanWA
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal kirim WA: ' . $e->getMessage());
        }

        return response()->json(['status' => 200, 'message' => 'Disposisi berhasil disimpan & Notifikasi WA terkirim!'], 200);
    }

    public function getLogs()
    {
        $logs = ActivityLog::orderBy('id', 'desc')->get();
        return response()->json(['status' => 200, 'data' => $logs], 200);
    }

    public function parsePDF(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('akses-admin');

        $request->validate([
            'file_pdf' => 'required|mimes:pdf|max:10000',
        ]);

        try {
            set_time_limit(240); 

            $file = $request->file('file_pdf');
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                return response()->json(['status' => 500, 'message' => 'GEMINI_API_KEY belum dipasang di file .env'], 500);
            }

            $pdfBase64 = base64_encode(file_get_contents($file->path()));

            $prompt = "Kamu adalah sistem AI pintar kearsipan dinas kepolisian. Tugasmu wajib menganalisis file dokumen visual PDF/Scan terlampir, lalu lakukan OCR dan ambil data: no_surat, tanggal_masuk, dari, kepada, dan perihal.\n\n"
                    . "Aturan penting:\n"
                    . "1. Format tanggal_masuk WAJIB berformat YYYY-MM-DD. Jika dokumen hanya menyebutkan bulan dan tahun seperti 'Mei 2026', ubah otomatis menjadi tanggal 1 yaitu '2026-05-01'.\n"
                    . "2. Jika data tidak ditemukan, isi properti tersebut dengan string kosong atau null.\n"
                    . "3. Berikan hasilnya murni dalam bentuk JSON objek langsung tanpa markdown backtick.";

            // KUNCI UTAMA: Gunakan model 'gemini-2.5-flash' yang paling stabil untuk multimodal visual OCR di v1beta
            $response = Http::withoutVerifying()
                ->timeout(180) 
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inlineData' => [
                                        'mimeType' => 'application/pdf',
                                        'data' => $pdfBase64
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Gagal konek ke Gemini. Detail: ' . $response->body()
                ], 500);
            }

            $resultJson = $response->json('candidates.0.content.parts.0.text');
            
            $cleanJson = preg_replace('/^```json\s*|```\s*$/i', '', trim($resultJson));
            $extractedData = json_decode($cleanJson, true);

            if (!$extractedData) {
                return response()->json([
                    'status' => 500,
                    'message' => 'AI gagal memformat JSON murni. Output mentah: ' . substr($cleanJson, 0, 100)
                ], 500);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Dokumen berhasil discan oleh AI!',
                'data' => $extractedData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Sistem Error saat memproses PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}