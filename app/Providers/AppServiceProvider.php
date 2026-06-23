<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Daftarkan aturan hak akses eksklusif untuk Admin
        Gate::define('akses-admin', function (User $user) {
            return $user->role === 'admin';
        });
    }
}