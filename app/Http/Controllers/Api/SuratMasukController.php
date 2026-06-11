<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        // File Upload Handling
        $fileName = null;
        if ($request->hasFile('file_pdf') && $request->file('file_pdf')->isValid()) {
            $file = $request->file('file_pdf');
            $fileName = time() . '_' . $file->getRandomName();
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
        ActivityLog::create([
            'aksi'    => 'Pemberian Disposisi',
            'rincian' => "Operator memperbarui instruksi disposisi pada surat nomor {$surat->no_surat}"
        ]);

        // ==========================================
        // WHATSAPP GATEWAY (NOTIFIKASI FONNTE)
        // ==========================================
        $pesanWA = "📩 *NOTIFIKASI DISPOSISI BARU* 📩\n\n"
                 . "Terdapat instruksi baru dari Pimpinan untuk segera ditindaklanjuti.\n\n"
                 . "📌 *No Surat:* " . $surat->no_surat . "\n"
                 . "🏢 *Dari:* " . $surat->dari . "\n"
                 . "📝 *Perihal:* " . $surat->perihal . "\n\n"
                 . "🔸 *Instruksi Kabag:* " . ($surat->disposisi_kabag ?: '-') . "\n"
                 . "🔸 *Instruksi Kasubag:* " . ($surat->disposisi_kasubag ?: '-') . "\n\n"
                 . "Silakan cek sistem InterOps-Hub untuk mengunduh berkas.";

        try {
            // Using Laravel's built-in HTTP client wrapper instead of cURL
            Http::withHeaders([
                'Authorization' => '2cSbEX1edJb2dWz59iQn'
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target'  => '081219408823',
                'message' => $pesanWA
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal kirim WA: ' . $e->getMessage());
        }

        return response()->json(['status' => 200, 'message' => 'Disposisi disimpan & Notifikasi WA terkirim'], 200);
    }

    public function getLogs()
    {
        $logs = ActivityLog::orderBy('id', 'desc')->get();
        return response()->json(['status' => 200, 'data' => $logs], 200);
    }
}