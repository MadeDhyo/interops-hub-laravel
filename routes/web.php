<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\SuratMasukController;
use App\Http\Controllers\Api\UserController;

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::get('/login', [AuthController::class, 'index']);
Route::post('/login/attempt', [AuthController::class, 'attemptLogin']);
Route::get('/logout', [AuthController::class, 'logout']);

// Rute Terproteksi (Wajib Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    Route::get('/surat-masuk', function () {
        return view('surat_masuk');
    });

    Route::get('/surat-keluar', function () {
        return view('surat_keluar');
    });

    Route::get('/activity-logs', function () {
        return view('activity_logs');
    });

    Route::get('/user-management', function () {
        return view('user_management');
    });

    Route::post('/api/surat-masuk/update/{id}', [SuratMasukController::class, 'updateDisposisi']);
    Route::get('/api/users', [UserController::class, 'index']);
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getSlaStats']);
});