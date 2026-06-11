<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SuratMasukController;
use App\Http\Controllers\Api\SuratKeluarController;

Route::get('/surat-masuk', [SuratMasukController::class, 'index']);
Route::post('/surat-masuk', [SuratMasukController::class, 'create']);
Route::post('/surat-masuk/update/{id}', [SuratMasukController::class, 'updateDisposisi']);
Route::get('/logs', [SuratMasukController::class, 'getLogs']);

Route::get('/surat-keluar', [SuratKeluarController::class, 'index']);
Route::post('/surat-keluar', [SuratKeluarController::class, 'create']);