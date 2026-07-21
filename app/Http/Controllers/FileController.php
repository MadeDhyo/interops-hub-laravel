<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function show($filename)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak. Anda harus login untuk melihat dokumen rahasia.');
        }

        $path = 'arsip_pdf/' . $filename;
        
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        return Storage::disk('local')->response($path);
    }
}
