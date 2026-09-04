<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'role',
        'subbag',
    ];

    protected $hidden = [
        'password',
    ];

    // ─── Role Helpers ───────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKabag(): bool
    {
        return $this->role === 'kabag';
    }

    public function isKasubbag(): bool
    {
        return $this->role === 'kasubbag';
    }

    public function isAnggota(): bool
    {
        return $this->role === 'anggota';
    }

    public function isUrmin(): bool
    {
        return $this->subbag === 'urmin';
    }

    /** Apakah user ini punya akses lihat semua subbag? */
    public function canSeeAllSubbag(): bool
    {
        return $this->isAdmin() || $this->isUrmin() || $this->isKabag();
    }

    /** Apakah user ini bisa input surat masuk baru? */
    public function canInputSuratMasuk(): bool
    {
        return $this->isAdmin() || ($this->isAnggota() && $this->isUrmin());
    }

    /** Apakah user ini bisa melakukan disposisi pada surat masuk? */
    public function canDisposisi(): bool
    {
        return $this->isAdmin() || $this->isKasubbag() || $this->isKabag();
    }

    /** Apakah user ini bisa melakukan paraf/persetujuan pada surat keluar? */
    public function canParaf(): bool
    {
        return $this->isAdmin() || $this->isKabag();
    }
}