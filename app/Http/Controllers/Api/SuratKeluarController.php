<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratKeluar;
use App\Models\ActivityLog;

class SuratKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratKeluar::query();

        // 1. Fuzzy Search Filtering
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('kepada', 'like', "%{$search}%")
                  ->orWhere('dari', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('no_surat', 'like', "%{$search}%");
            });
        }

        // 2. Date Filtering
        if ($startDate = $request->input('start_date')) {
            $query->where('tanggal_surat', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->where('tanggal_surat', '<=', $endDate);
        }

        // 3. Pagination Setup
        $limit = $request->input('limit', 10);
        $paginatedData = $query->orderBy('id', 'desc')->paginate($limit);

        // 4. Global Stats to prevent JavaScript component crashes
        $stats = [
            'total' => SuratKeluar::count()
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
        // Native Laravel File Upload Handling
        $fileName = null;
        if ($request->hasFile('file_pdf') && $request->file('file_pdf')->isValid()) {
            $file = $request->file('file_pdf');
            $fileName = time() . '_' . $file->getClientOriginalName();
            // Moves uploaded file to public/uploads directory directly
            $file->move(public_path('uploads'), $fileName);
        }

        $surat = SuratKeluar::create([
            'kepada'        => $request->input('kepada'),
            'no_surat'      => $request->input('no_surat'),
            'tanggal_surat' => $request->input('tanggal_surat'),
            'dari'          => $request->input('dari'),
            'tanggal_input' => $request->input('tanggal_input'),
            'perihal'       => $request->input('perihal'),
            'file_pdf'      => $fileName
        ]);

        // Audit Trail Log Capture
        ActivityLog::create([
            'aksi'    => 'Input Surat Keluar',
            'rincian' => "Operator menginput surat keluar nomor {$surat->no_surat} ditujukan ke {$surat->kepada}"
        ]);

        return response()->json([
            'status' => 201, 
            'message' => 'Surat keluar berhasil disimpan'
        ], 201);
    }
}