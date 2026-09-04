<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Admin only
        Gate::define('akses-admin', fn(User $user) => $user->isAdmin());

        // Kabag
        Gate::define('akses-kabag', fn(User $user) => $user->isAdmin() || $user->isKabag());

        // Kasubbag atau admin
        Gate::define('akses-kasubbag', fn(User $user) => $user->isAdmin() || $user->isKasubbag());

        // Bisa input surat masuk baru: anggota urmin atau admin
        Gate::define('bisa-input-surma', fn(User $user) => $user->canInputSuratMasuk());

        // Bisa disposisi: kasubbag, kabag, atau admin
        Gate::define('bisa-disposisi', fn(User $user) => $user->canDisposisi());

        // Bisa paraf/approve surat keluar: kabag atau admin
        Gate::define('bisa-paraf-kabag', fn(User $user) => $user->canParaf());

        // Bisa input surat keluar: anggota, kasubbag, urmin, atau admin (KABAG TIDAK BISA)
        Gate::define('bisa-input-surkel', fn(User $user) => !$user->isKabag());

        // Bisa lihat semua subbag: urmin, kabag, atau admin
        Gate::define('lihat-semua-subbag', fn(User $user) => $user->canSeeAllSubbag());
    }
}