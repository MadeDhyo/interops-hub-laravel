<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\SuratMasukController;

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
});