<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratKeluar;
use App\Models\DailySignature;
use App\Traits\Loggable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    use Loggable;

    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = SuratKeluar::query();

        // Scope by subbag (kecuali urmin/kabag/admin yang bisa lihat semua)
        $subbagFilter = null;
        if ($user->canSeeAllSubbag()) {
            if ($filterSubbag = $request->input('subbag')) {
                $subbagFilter = $filterSubbag;
            }
        } else {
            $subbagFilter = $user->subbag;
        }

        if ($subbagFilter) {
            $query->where('subbag', $subbagFilter);
        }

        // Filter status_paraf_kabag jika diminta
        if ($statusParaf = $request->input('status_paraf')) {
            if ($statusParaf === 'urgent') {
                $query->where('status_paraf_kabag', 'pending')
                      ->where('created_at', '<=', \Carbon\Carbon::now()->subDays(3));
            } else {
                $query->where('status_paraf_kabag', $statusParaf);
            }
        }

        // 1. Fuzzy Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('kepada', 'like', "%{$search}%")
                  ->orWhere('dari', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('no_surat', 'like', "%{$search}%")
                  ->orWhere('keterangan_tujuan', 'like', "%{$search}%")
                  ->orWhere('full_text_content', 'like', "%{$search}%");
            });
        }

        // 2. Date Filtering
        if ($startDate = $request->input('start_date')) $query->where('tanggal_surat', '>=', $startDate);
        if ($endDate   = $request->input('end_date'))   $query->where('tanggal_surat', '<=', $endDate);

        // 3. Pagination
        $limit         = $request->input('limit', 10);
        $paginatedData = $query->orderBy('id', 'desc')->paginate($limit);

        // Stats via single conditional aggregation query
        $statsBase = SuratKeluar::query();
        if ($subbagFilter) $statsBase->where('subbag', $subbagFilter);
        $statsRow = $statsBase->selectRaw(
            'COUNT(*) as total,
             SUM(CASE WHEN status_paraf_kabag = ? THEN 1 ELSE 0 END) as pending,
             SUM(CASE WHEN status_paraf_kabag = ? THEN 1 ELSE 0 END) as disetujui',
            ['pending', 'disetujui']
        )->first();

        return response()->json([
            'status' => 200,
            'data'   => $paginatedData->items(),
            'stats'  => [
                'total'     => (int) ($statsRow->total     ?? 0),
                'pending'   => (int) ($statsRow->pending   ?? 0),
                'disetujui' => (int) ($statsRow->disetujui ?? 0),
            ],
            'pagination' => [
                'page'        => $paginatedData->currentPage(),
                'total_pages' => $paginatedData->lastPage(),
            ],
        ], 200);
    }

    public function exportCsv(Request $request)
    {
        $user  = auth()->user();
        $query = SuratKeluar::query();

        $subbagFilter = $user->canSeeAllSubbag() ? $request->input('subbag') : $user->subbag;
        if ($subbagFilter) $query->where('subbag', $subbagFilter);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('kepada', 'like', "%{$search}%")
                  ->orWhere('dari', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('no_surat', 'like', "%{$search}%");
            });
        }
        if ($startDate = $request->input('start_date')) $query->where('tanggal_surat', '>=', $startDate);
        if ($endDate   = $request->input('end_date'))   $query->where('tanggal_surat', '<=', $endDate);

        $data    = $query->orderBy('id', 'desc')->get();
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="surat_keluar_' . date('Y-m-d_His') . '.csv"',
        ];
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['No Surat', 'Kepada', 'Dari', 'Perihal', 'Tanggal Surat', 'Tanggal Input', 'Subbag', 'Keterangan Tujuan', 'Status Paraf Kabag']);
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->no_surat, $row->kepada, $row->dari, $row->perihal,
                    $row->tanggal_surat, $row->tanggal_input,
                    strtoupper($row->subbag ?? '-'),
                    $row->keterangan_tujuan ?? '-',
                    strtoupper($row->status_paraf_kabag ?? 'PENDING'),
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function create(Request $request)
    {
        Gate::authorize('bisa-input-surkel');
        $user = auth()->user();

        $request->validate([
            'file_pdf'         => 'nullable|file|mimes:pdf|max:10240',
            'keterangan_tujuan'=> 'nullable|string|max:1000',
        ]);

        $fileName = null;
        $extractedText = null;

        if ($request->hasFile('file_pdf') && $request->file('file_pdf')->isValid()) {
            $file     = $request->file('file_pdf');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('arsip_pdf', $fileName, 'local');
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($file->path());
                $extractedText = $pdf->getText();
            } catch (\Exception $e) {
                $extractedText = null;
            }
        }

        // Auto-tag subbag dari user yang login
        $subbag = $user->subbag ?? null;

        $surat = SuratKeluar::create([
            'kepada'             => $request->input('kepada'),
            'no_surat'           => $request->input('no_surat'),
            'tanggal_surat'      => $request->input('tanggal_surat'),
            'dari'               => $request->input('dari'),
            'tanggal_input'      => $request->input('tanggal_input'),
            'perihal'            => $request->input('perihal'),
            'file_pdf'           => $fileName,
            'full_text_content'  => $extractedText,
            'subbag'             => $subbag,
            'keterangan_tujuan'  => $request->input('keterangan_tujuan'),
            'status_paraf_kabag' => 'pending',
        ]);

        $this->logActivity(
            'Input Surat Keluar',
            "Menginput surat keluar nomor {$surat->no_surat} ditujukan ke {$surat->kepada}",
            $subbag
        );

        return response()->json(['status' => 201, 'message' => 'Surat keluar berhasil disimpan'], 201);
    }

    /**
     * Cek apakah user (Kabag) sudah memiliki TTD digital untuk hari ini.
     */
    public function getTodaySignature()
    {
        $user = auth()->user();
        $todaySig = DailySignature::where('user_id', $user->id)
            ->where('signature_date', now()->toDateString())
            ->first();

        return response()->json([
            'status'        => 200,
            'has_signature' => (bool)$todaySig,
            'signature_path'=> $todaySig ? $todaySig->signature_path : null,
            'date'          => now()->toDateString(),
        ], 200);
    }

    /**
     * Paraf/Persetujuan Surat Keluar oleh KABAG.
     * Menggunakan konsep Reusable Daily Signature: jika TTD hari ini sudah ada, otomatis dipakai ulang.
     */
    public function parafKabag(Request $request, $id)
    {
        Gate::authorize('bisa-paraf-kabag');

        $request->validate([
            'status'           => 'required|in:disetujui,ditolak',
            'catatan'          => 'nullable|string|max:1000',
            'signature_base64' => 'nullable|string',
        ]);

        $surat = SuratKeluar::findOrFail($id);
        $user  = auth()->user();
        $today = now()->toDateString();

        $dailySig = DailySignature::where('user_id', $user->id)
            ->where('signature_date', $today)
            ->first();

        $signaturePath = $dailySig ? $dailySig->signature_path : null;

        // Jika ada input canvas TTD baru untuk hari ini
        if ($request->filled('signature_base64')) {
            $base64 = $request->input('signature_base64');
            
            // Validate basic data uri format
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
                $type = strtolower($type[1]);
                
                $base64 = base64_decode($base64);
                if ($base64 !== false) {
                    $fileName = 'sig_' . $user->id . '_' . $today . '_' . time() . '.' . $type;
                    Storage::disk('local')->put('signatures/' . $fileName, $base64);
                    $signaturePath = 'signatures/' . $fileName;

                    // Simpan atau update TTD harian
                    $dailySig = DailySignature::updateOrCreate(
                        ['user_id' => $user->id, 'signature_date' => $today],
                        ['signature_path' => $signaturePath]
                    );
                }
            }
        }

        // Jika status disetujui tapi belum ada TTD hari ini dan tidak upload TTD baru
        if ($request->status === 'disetujui' && !$signaturePath) {
            return response()->json([
                'status'  => 422,
                'message' => 'Anda belum mengupload/mengisi TTD untuk hari ini. Silakan upload TTD sekali untuk hari ini.',
            ], 422);
        }

        $surat->update([
            'status_paraf_kabag' => $request->status,
            'paraf_kabag_at'     => now(),
            'catatan_kabag'      => $request->catatan,
            'paraf_path'         => $request->status === 'disetujui' ? $signaturePath : null,
        ]);

        $statusText = $request->status === 'disetujui' ? 'Menyetujui/Memaraf' : 'Menolak';
        $this->logActivity(
            'Paraf Surat Keluar',
            "{$statusText} surat keluar nomor {$surat->no_surat} (Subbag: " . strtoupper($surat->subbag ?? '-') . ")",
            'kabag'
        );

        return response()->json([
            'status'         => 200,
            'message'        => "Surat keluar berhasil di-{$request->status} oleh KABAG.",
            'used_daily_sig' => (bool)$dailySig,
        ], 200);
    }
}