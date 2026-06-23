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
            // SLA check: pending status and older than or equal to 3 days
            'sla'     => SuratMasuk::where('status', 'pending')
                            ->where('tanggal_masuk', '<=', Carbon::now()->subDays(3))
                            ->count()
        ];

        return response()->json([
            'status' => 200,
            'data' => $paginatedData->items(),
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

        // 1. Validasi file input (Maksimal 10MB)
        $request->validate([
            'file_pdf' => 'required|mimes:pdf|max:10000',
        ]);

        try {
            $file = $request->file('file_pdf');
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                return response()->json([
                    'status' => 500,
                    'message' => 'Konfigurasi GEMINI_API_KEY belum dipasang di file .env'
                ], 500);
            }

            // 2. Ekstrak teks langsung dari dokumen PDF menggunakan Smalot PdfParser
            $parser = new Parser();
            $pdfDocument = $parser->parseFile($file->path());
            $pdfTextContent = $pdfDocument->getText();

            // Antisipasi jika PDF berupa hasil scan gambar penuh (kosong tanpa teks)
            if (trim($pdfTextContent) === '') {
                return response()->json([
                    'status' => 422,
                    'message' => 'Gagal membaca teks dokumen. File PDF kemungkinan berupa hasil scan gambar murni tanpa layer teks.'
                ], 422);
            }
            
            // 3. Susun instruksi ketat agar AI melakukan ekstraksi data ke format JSON
            $prompt = "Kamu adalah sistem AI pintar untuk manajemen kearsipan surat dinas resmi di lingkungan kepolisian/instansi pemerintah.\n"
                    . "Tugasmu adalah menganalisis teks dari sebuah surat masuk yang dilampirkan, lalu mengekstrak informasi penting secara akurat.\n\n"
                    . "Konteks Teks Dokumen Surat:\n"
                    . "--- START TEXT ---\n"
                    . $pdfTextContent . "\n"
                    . "--- END TEXT ---\n\n"
                    . "Aturan Ekstraksi:\n"
                    . "1. Format tanggal wajib diubah ke format standar basis data: YYYY-MM-DD (Contoh: '30 Maret 2026' menjadi '2026-03-30').\n"
                    . "2. Jika informasi tertentu (seperti nomor surat atau perihal) benar-benar tidak tertulis di teks, berikan nilai null.\n"
                    . "3. Jangan berikan teks pembuka atau penutup apa pun. Kembalikan HASILNYA HANYA DALAM FORMAT JSON MURNI.\n\n"
                    . "Struktur Objek JSON yang Wajib Diikuti:\n"
                    . "{\n"
                    . "  \"no_surat\": \"Nomor surat resmi yang tertera\",\n"
                    . "  \"tanggal_masuk\": \"Tanggal surat dibuat atau diterima dalam format YYYY-MM-DD\",\n"
                    . "  \"dari\": \"Nama instansi, divisi, atau perorangan pengirim surat\",\n"
                    . "  \"kepada\": \"Nama jabatan atau instansi tujuan surat\",\n"
                    . "  \"perihal\": \"Ringkasan lengkap perihal atau subjek surat\"\n"
                    . "}";

            // 4. Kirim data teks prompt murni ke API Gemini 1.5 Flash
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
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
                    'message' => 'Gagal mendapatkan respon dari layanan API Gemini. Status internal: ' . $response->status()
                ], 500);
            }

            // 5. Ambil teks hasil parsing JSON dari respon AI
            $resultJson = $response->json('candidates.0.content.parts.0.text');
            $extractedData = json_decode($resultJson, true);

            return response()->json([
                'status' => 200,
                'message' => 'Analisis dokumen surat berhasil diproses oleh AI',
                'data' => $extractedData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan sistem saat memproses dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
}