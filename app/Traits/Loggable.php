<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    /**
     * Catat aktivitas personel secara otomatis sesuai skema tabel database.
     */
    public function logActivity($aksi, $deskripsi)
    {
        // Ambil nama lengkap pelaku yang sedang login, atau fallback ke 'Sistem Admin'
        $operator = Auth::user() ? Auth::user()->nama_lengkap : 'Sistem Admin';

        // Masukkan data murni yang didukung oleh struktur kolom migration lu bray
        ActivityLog::create([
            'aksi'    => $aksi,
            'rincian' => "[$operator] " . $deskripsi, // Info nama digabung kesini biar aman & informatif
        ]);
    }
}