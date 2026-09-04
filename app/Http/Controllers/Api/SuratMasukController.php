<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use App\Models\SuratMasukSubbag;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Traits\Loggable;

class SuratMasukController extends Controller
{
    use Loggable;

    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = SuratMasuk::with('subbags');

        // --- Subbag filter: scope per subbag kecuali urmin/admin ---
        $subbagFilter = null;

        if ($user->canSeeAllSubbag()) {
            // Urmin & admin: bisa filter via ?subbag= atau lihat semua
            if ($filterSubbag = $request->input('subbag')) {
                $subbagFilter = $filterSubbag;
            }
        } else {
            // Kasubbag/anggota subbag tertentu: hanya subbag mereka
            $subbagFilter = $user->subbag;
        }

        if ($subbagFilter) {
            $query->whereHas('subbags', fn($q) => $q->where('subbag', $subbagFilter));
        }

        // 1. Smart Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                  ->orWhere('dari', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('full_text_content', 'like', "%{$search}%");
            });
        }

        // 2. Date Filtering
        if ($startDate = $request->input('start_date')) {
            $query->where('tanggal_masuk', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->where('tanggal_masuk', '<=', $endDate);
        }

        // 3. Status Filter
        if ($status = $request->input('status')) {
            if ($status === 'pending') {
                $query->where('status', 'pending')->where('tanggal_masuk', '>', Carbon::now()->subDays(3));
            } elseif ($status === 'sla') {
                $query->where('status', 'pending')->where('tanggal_masuk', '<=', Carbon::now()->subDays(3));
            } elseif ($status === 'disposisi') {
                $query->where('status', 'disposisi');
            }
        }

        // 4. Pagination
        $limit = $request->input('limit', 10);
        $paginatedData = $query->orderBy('id', 'desc')->paginate($limit);

        // 5. Stats (scoped per subbag if applicable)
        $statsQuery = SuratMasuk::query();
        if ($subbagFilter) {
            $statsQuery->whereHas('subbags', fn($q) => $q->where('subbag', $subbagFilter));
        }
        $stats = [
            'total'   => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'sla'     => (clone $statsQuery)->where('status', 'pending')
                            ->where('tanggal_masuk', '<=', Carbon::now()->subDays(3))->count(),
        ];

        // Append subbag_list to each row
        $rows = collect($paginatedData->toArray()['data'])->map(function($row) {
            $pivots = SuratMasukSubbag::where('surat_masuk_id', $row['id'])->pluck('subbag')->toArray();
            $row['subbag_list'] = $pivots;
            return $row;
        });

        return response()->json([
            'status'     => 200,
            'data'       => $rows,
            'stats'      => $stats,
            'pagination' => [
                'page'        => $paginatedData->currentPage(),
                'total_pages' => $paginatedData->lastPage(),
            ],
        ], 200);
    }

    public function exportCsv(Request $request)
    {
        $user  = auth()->user();
        $query = SuratMasuk::with('subbags');

        $subbagFilter = $user->canSeeAllSubbag()
            ? $request->input('subbag')
            : $user->subbag;

        if ($subbagFilter) {
            $query->whereHas('subbags', fn($q) => $q->where('subbag', $subbagFilter));
        }

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                  ->orWhere('dari', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }
        if ($startDate = $request->input('start_date')) $query->where('tanggal_masuk', '>=', $startDate);
        if ($endDate = $request->input('end_date')) $query->where('tanggal_masuk', '<=', $endDate);

        $data = $query->orderBy('id', 'desc')->get();
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="surat_masuk_' . date('Y-m-d_His') . '.csv"',
        ];
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['No Surat', 'Dari', 'Kepada', 'Perihal', 'Tanggal Masuk', 'Status', 'Subbag Tujuan', 'Disposisi Kabag', 'Disposisi Kasubag']);
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->no_surat, $row->dari, $row->kepada, $row->perihal,
                    $row->tanggal_masuk, $row->status,
                    $row->subbags->pluck('subbag')->implode(', '),
                    $row->disposisi_kabag ?? '-',
                    $row->disposisi_kasubag ?? '-',
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function create(Request $request)
    {
        Gate::authorize('bisa-input-surma');

        $request->validate([
            'file_pdf'    => 'nullable|file|mimes:pdf|max:10240',
            'tujuan_subbag' => 'required|array|min:1|max:2',
            'tujuan_subbag.*' => 'in:bhi,bi,ops,koor,urmin',
        ]);

        $fileName = null;
        $extractedText = null;

        if ($request->hasFile('file_pdf') && $request->file('file_pdf')->isValid()) {
            $file = $request->file('file_pdf');
            $fileName = time() . '_' . $file->hashName();
            $file->storeAs('arsip_pdf', $fileName, 'local');
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($file->path());
                $extractedText = $pdf->getText();
            } catch (\Exception $e) {
                $extractedText = null;
            }
        }

        $surat = SuratMasuk::create([
            'kepada'            => $request->input('kepada'),
            'dari'              => $request->input('dari'),
            'perihal'           => $request->input('perihal'),
            'tanggal_masuk'     => $request->input('tanggal_masuk'),
            'no_surat'          => $request->input('no_surat'),
            'no_dispo'          => null,
            'file_pdf'          => $fileName,
            'status'            => 'pending',
            'full_text_content' => $extractedText,
        ]);

        // Simpan ke pivot subbag (1-2 subbag)
        $subbagList = $request->input('tujuan_subbag');
        foreach ($subbagList as $subbag) {
            SuratMasukSubbag::create([
                'surat_masuk_id' => $surat->id,
                'subbag'         => $subbag,
            ]);
            // Log per subbag tujuan
            $this->logActivity(
                'Input Surat Masuk',
                "Urmin menginput surat {$surat->no_surat} dari {$surat->dari} → Subbag " . strtoupper($subbag),
                $subbag
            );
        }

        return response()->json(['status' => 201, 'message' => 'Surat masuk berhasil disimpan'], 201);
    }

    public function updateDisposisi(Request $request, $id)
    {
        Gate::authorize('bisa-disposisi');

        $request->validate([
            'disposisi_kabag'   => 'required|string',
            'disposisi_kasubag' => 'nullable|string',
        ]);

        try {
            $surat = SuratMasuk::with('subbags')->findOrFail($id);

            // Kasubbag non-admin/non-kabag hanya bisa disposisi surat yang masuk ke subbag mereka
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isKabag()) {
                $subbagSurat = $surat->subbags->pluck('subbag')->toArray();
                if (!in_array($user->subbag, $subbagSurat)) {
                    return response()->json(['status' => 403, 'message' => 'Anda tidak berwenang mendisposisi surat ini.'], 403);
                }
            }

            $romawiBulan  = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
            $romawi       = $romawiBulan[date('n') - 1];
            $autoNoDispo  = 'DSP/' . date('Y') . '/' . $romawi . '/' . rand(1000, 9999);

            $surat->update([
                'status'             => 'disposisi',
                'no_dispo'           => $autoNoDispo,
                'disposisi_kabag'    => $request->disposisi_kabag,
                'disposisi_kasubag'  => $request->disposisi_kasubag,
            ]);

            $this->logActivity(
                'Kirim Disposisi',
                "{$user->nama_lengkap} menerbitkan nota disposisi: {$autoNoDispo} untuk surat {$surat->no_surat}",
                $user->subbag
            );

            return response()->json(['status' => 200, 'message' => 'Lembar disposisi berhasil diterbitkan!'], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Gagal memproses disposisi: ' . $e->getMessage()], 500);
        }
    }

    public function getLogs(Request $request)
    {
        $user  = auth()->user();
        $query = ActivityLog::orderBy('id', 'desc');

        if (!$user->canSeeAllSubbag() && !$user->isAdmin()) {
            $query->where('subbag', $user->subbag);
        } elseif ($filterSubbag = $request->input('subbag')) {
            $query->where('subbag', $filterSubbag);
        }

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('aksi', 'like', "%{$search}%")
                  ->orWhere('rincian', 'like', "%{$search}%")
                  ->orWhere('subbag', 'like', "%{$search}%");
            });
        }

        $logs = $query->get();
        return response()->json(['status' => 200, 'data' => $logs], 200);
    }

    public function parsePDF(Request $request)
    {
        Gate::authorize('bisa-input-surma');

        $request->validate(['file_pdf' => 'required|mimes:pdf|max:10000']);

        try {
            set_time_limit(240);
            $file   = $request->file('file_pdf');
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

            $response = Http::withoutVerifying()->timeout(180)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt], ['inlineData' => ['mimeType' => 'application/pdf', 'data' => $pdfBase64]]]]],
                    'generationConfig' => ['responseMimeType' => 'application/json'],
                ]);

            if ($response->failed()) {
                return response()->json(['status' => 500, 'message' => 'Gagal konek ke Gemini. Detail: ' . $response->body()], 500);
            }

            $resultJson    = $response->json('candidates.0.content.parts.0.text');
            $cleanJson     = preg_replace('/^```json\s*|```\s*$/i', '', trim($resultJson));
            $extractedData = json_decode($cleanJson, true);

            if (!$extractedData) {
                return response()->json(['status' => 500, 'message' => 'AI gagal memformat JSON murni.'], 500);
            }

            $this->logActivity('AI OCR Scan', 'Sistem AI mengekstrak payload dokumen secara visual via Gemini API');

            return response()->json(['status' => 200, 'message' => 'Dokumen berhasil discan oleh AI!', 'data' => $extractedData], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Sistem Error: ' . $e->getMessage()], 500);
        }
    }
}