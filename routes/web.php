<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\SuratMasukController;
use App\Http\Controllers\Api\SuratKeluarController;
use App\Http\Controllers\Api\UserController;

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::get('/login', [AuthController::class, 'index']);
Route::post('/login/attempt', [AuthController::class, 'attemptLogin']);
Route::get('/logout', [AuthController::class, 'logout']);

// Rute Terproteksi (Wajib Login & Mendukung Session Cookie JQuery)
Route::middleware(['auth'])->group(function () {
    // ----------------- RUTE TAMPILAN (VIEWS) -----------------
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

    // ==========================================
    // ENDPOINT AJAX JQUERY DATA STREAM
    // ==========================================
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getSlaStats']);
    Route::get('/api/users', [UserController::class, 'index']);

    // Surat Masuk API Endpoints (Bebas Typo)
    Route::get('/api/surat-masuk', [SuratMasukController::class, 'index']);
    Route::post('/api/surat-masuk', [SuratMasukController::class, 'create']);
    Route::post('/api/surat-masuk/update/{id}', [SuratMasukController::class, 'updateDisposisi']);
    Route::post('/api/surat-masuk/parse', [SuratMasukController::class, 'parsePDF']);
    
    // Surat Keluar & Logs API Endpoints
    Route::get('/api/surat-keluar', [SuratKeluarController::class, 'index']);
    Route::post('/api/surat-keluar', [SuratKeluarController::class, 'create']);
    Route::get('/api/logs', [SuratMasukController::class, 'getLogs']);
});