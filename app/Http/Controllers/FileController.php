<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;

class FileController extends Controller
{
    public function show($filename)
    {
        if (!Auth::check()) {
            abort(403, 'Akses ditolak. Anda harus login untuk melihat dokumen rahasia.');
        }

        $user = Auth::user();
        $safeName = basename($filename);
        $path = 'arsip_pdf/' . $safeName;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        // Pimpinan & Urmin & Admin memiliki akses penuh ke seluruh arsip
        if ($user->canSeeAllSubbag()) {
            return Storage::disk('local')->response($path);
        }

        // Cek otorisasi subbag untuk Kasubbag & Anggota
        $hasSuratMasuk = SuratMasuk::where('file_pdf', $safeName)
            ->whereHas('subbags', fn($q) => $q->where('subbag', $user->subbag))
            ->exists();

        if ($hasSuratMasuk) {
            return Storage::disk('local')->response($path);
        }

        $hasSuratKeluar = SuratKeluar::where('file_pdf', $safeName)
            ->where('subbag', $user->subbag)
            ->exists();

        if ($hasSuratKeluar) {
            return Storage::disk('local')->response($path);
        }

        abort(403, 'Akses ditolak. Anda tidak berwenang melihat dokumen subbag lain.');
    }
}
