<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use App\Models\SuratMasukSubbag;
use App\Models\TindakLanjutSuratMasuk;
use App\Models\ActivityLog;
use App\Jobs\ProcessOcrSuratMasuk;
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
        $query = SuratMasuk::with(['subbags', 'tindakLanjut']);

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

        // 4. PIC Filter
        if ($pic = $request->input('pic')) {
            $query->where('disposisi_kasubag', 'like', "%{$pic}%");
        }

        // 5. Pagination
        $limit = $request->input('limit', 10);
        $paginatedData = $query->orderBy('id', 'desc')->paginate($limit);

        // 6. Stats (1 query via conditional aggregation)
        $statsBase = SuratMasuk::query();
        if ($subbagFilter) {
            $statsBase->whereHas('subbags', fn($q) => $q->where('subbag', $subbagFilter));
        }
        $statsRow = $statsBase->selectRaw(
            'COUNT(*) as total,
             SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
             SUM(CASE WHEN status = ? AND tanggal_masuk <= ? THEN 1 ELSE 0 END) as sla',
            ['pending', 'pending', Carbon::now()->subDays(3)->toDateString()]
        )->first();
        $stats = [
            'total'   => (int) ($statsRow->total   ?? 0),
            'pending' => (int) ($statsRow->pending  ?? 0),
            'sla'     => (int) ($statsRow->sla      ?? 0),
        ];

        // Append subbag_list and extracted pic
        $rows = $paginatedData->getCollection()->map(function($surat) {
            $arr = $surat->toArray();
            $arr['subbag_list'] = $surat->subbags->pluck('subbag')->toArray();

            $pic = null;
            if ($surat->disposisi_kasubag) {
                if (preg_match('/\[PIC:\s*([^\]]+)\]/i', $surat->disposisi_kasubag, $matches)) {
                    $pic = trim($matches[1]);
                } elseif (preg_match('/^PIC:\s*([^\.\n\r]+)/i', $surat->disposisi_kasubag, $matches)) {
                    $pic = trim($matches[1]);
                }
            }
            $arr['pic'] = $pic;

            return $arr;
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
        if ($pic = $request->input('pic')) $query->where('disposisi_kasubag', 'like', "%{$pic}%");

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

        if ($request->hasFile('file_pdf') && $request->file('file_pdf')->isValid()) {
            $file = $request->file('file_pdf');
            $fileName = time() . '_' . $file->hashName();
            $file->storeAs('arsip_pdf', $fileName, 'local');
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
            'full_text_content' => null,
        ]);

        // Dispatch background OCR job jika ada file
        if ($fileName) {
            ProcessOcrSuratMasuk::dispatch($surat->id, 'arsip_pdf/' . $fileName);
        }

        // Simpan ke pivot subbag (1-2 subbag)
        $subbagList = $request->input('tujuan_subbag');
        foreach ($subbagList as $subbag) {
            SuratMasukSubbag::create([
                'surat_masuk_id' => $surat->id,
                'subbag'         => $subbag,
            ]);
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

        $limit = $request->input('limit', 50);
        $paginated = $query->paginate($limit);
        return response()->json([
            'status' => 200,
            'data'   => $paginated->items(),
            'pagination' => [
                'page'        => $paginated->currentPage(),
                'total_pages' => $paginated->lastPage(),
                'total'       => $paginated->total(),
            ],
        ], 200);
    }

    public function parsePDF(Request $request)
    {
        Gate::authorize('bisa-input-surma');

        $request->validate(['file_pdf' => 'required|mimes:pdf|max:10000']);

        try {
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

            // Panggil Gemini tetap sync di sini karena user butuh data untuk mengisi form
            // (bukan untuk background indexing)
            $response = Http::withoutVerifying()->timeout(60)
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

    public function getTindakLanjut($id)
    {
        $surat = SuratMasuk::with(['subbags', 'tindakLanjut'])->findOrFail($id);
        $user  = auth()->user();

        // Admin, Kabag, Urmin (kasubbag & anggota), atau user di subbag tujuan surat
        if (!$user->canSeeAllSubbag()) {
            $subbagSurat = $surat->subbags->pluck('subbag')->toArray();
            if (!in_array($user->subbag, $subbagSurat)) {
                return response()->json(['status' => 403, 'message' => 'Akses ditolak.'], 403);
            }
        }

        return response()->json([
            'status' => 200,
            'data'   => $surat->tindakLanjut,
        ], 200);
    }

    public function storeTindakLanjut(Request $request, $id)
    {
        $user = auth()->user();

        // Hanya anggota yang boleh mengisi/edit tindak lanjut
        if ($user->role !== 'anggota') {
            return response()->json(['status' => 403, 'message' => 'Hanya anggota yang bisa mengisi tindak lanjut.'], 403);
        }

        $request->validate([
            'tipe_aksi'  => 'required|in:tindak_lanjut,arsip,buat_balasan,lainnya',
            'catatan'    => 'required|string|max:2000',
            'no_balasan' => 'nullable|string|max:255',
        ]);

        $surat = SuratMasuk::with('subbags')->findOrFail($id);

        // Cek surat harus milik subbag user
        $subbagSurat = $surat->subbags->pluck('subbag')->toArray();
        if (!in_array($user->subbag, $subbagSurat)) {
            return response()->json(['status' => 403, 'message' => 'Surat ini bukan milik subbag Anda.'], 403);
        }

        // Cek surat harus sudah disposisi
        if ($surat->status !== 'disposisi') {
            return response()->json(['status' => 422, 'message' => 'Surat belum didisposisi, tidak bisa ditindak lanjuti.'], 422);
        }

        $existing = TindakLanjutSuratMasuk::where('surat_masuk_id', $id)->first();
        $isEdit   = (bool) $existing;

        $tl = TindakLanjutSuratMasuk::updateOrCreate(
            ['surat_masuk_id' => $id],
            [
                'user_id'    => $user->id,
                'nama_user'  => $user->nama_lengkap,
                'tipe_aksi'  => $request->tipe_aksi,
                'no_balasan' => $request->no_balasan,
                'catatan'    => $request->catatan,
            ]
        );

        $tipeLabel = [
            'tindak_lanjut' => 'Tindak Lanjut',
            'arsip'         => 'Arsipkan',
            'buat_balasan'  => 'Buat Balasan',
            'lainnya'       => 'Lainnya',
        ];
        $label  = $tipeLabel[$request->tipe_aksi] ?? $request->tipe_aksi;
        $action = $isEdit ? 'mengedit tindak lanjut' : 'menindak lanjuti';

        $this->logActivity(
            'Tindak Lanjut Surat Masuk',
            "{$action} surat {$surat->no_surat} — Aksi: {$label}" . ($request->no_balasan ? " (No. Balasan: {$request->no_balasan})" : ''),
            $user->subbag
        );

        return response()->json([
            'status'  => 200,
            'message' => $isEdit ? 'Tindak lanjut berhasil diperbarui.' : 'Tindak lanjut berhasil disimpan.',
            'data'    => $tl,
        ], 200);
    }
}