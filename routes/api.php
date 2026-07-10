<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SuratMasukController;
use App\Http\Controllers\Api\SuratKeluarController;
use App\Http\Controllers\Api\UserController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/users', [UserController::class, 'store']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// Modul Surat Masuk
Route::get('/surat-masuk', [SuratMasukController::class, 'index']);
Route::post('/surat-masuk', [SuratMasukController::class, 'create']);
Route::post('/surat-masuk/parse', [SuratMasukController::class, 'parsePDF']);
Route::post('/scan-surat', [SuratMasukController::class, 'scanWithAI']);

// Modul Surat Keluar
Route::get('/surat-keluar', [SuratKeluarController::class, 'index']);
Route::post('/surat-keluar', [SuratKeluarController::class, 'create']);