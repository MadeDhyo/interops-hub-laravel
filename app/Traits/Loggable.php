<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    /**
     * Catat aktivitas personel secara otomatis, termasuk subbag pengguna.
     *
     * @param string $aksi     Judul singkat aksi (e.g. "Input Surat Masuk")
     * @param string $deskripsi  Rincian aksi
     * @param string|null $subbag  Override subbag (opsional, default dari user login)
     */
    public function logActivity(string $aksi, string $deskripsi, ?string $subbag = null): void
    {
        $user     = Auth::user();
        $operator = $user ? $user->nama_lengkap : 'Sistem';
        $userSubbag = $subbag ?? ($user ? $user->subbag : null);
        $userId   = $user ? $user->id : null;

        ActivityLog::create([
            'user_id'   => $userId,
            'nama_user' => $operator,
            'subbag'    => $userSubbag,
            'aksi'      => $aksi,
            'rincian'   => "[{$operator}] " . $deskripsi,
        ]);
    }
}