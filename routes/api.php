<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Cukup sisakan endpoint yang murni butuh Token Sanctum (misal buat integrasi mobile app nanti)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/users', [UserController::class, 'store']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});