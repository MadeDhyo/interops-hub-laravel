<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\SuratMasukController;
use App\Http\Controllers\Api\SuratKeluarController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login/attempt', [AuthController::class, 'attemptLogin']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/logout', [AuthController::class, 'logout']);

// UI Halaman Kunci (bisa diakses jika Auth, tapi tak terpengaruh session.lock)
Route::get('/locked', function() {
    if (!Auth::check()) return redirect('/login');
    if (session('locked') !== true) return redirect('/dashboard');
    return view('auth.locked');
})->name('locked');

Route::post('/api/auth/lock', function() {
    if (Auth::check()) session(['locked' => true]);
    return response()->json(['status' => 'locked']);
});

Route::post('/api/auth/unlock', function(\Illuminate\Http\Request $request) {
    if (!Auth::check()) return response()->json(['status' => 401, 'message' => 'Unauthorized'], 401);
    if (\Illuminate\Support\Facades\Hash::check($request->password, Auth::user()->password)) {
        session(['locked' => false]);
        return response()->json(['status' => 200, 'message' => 'Unlocked']);
    }
    return response()->json(['status' => 403, 'message' => 'Password salah!'], 403);
});

// ROUTE GROUP BERDASARKAN MIDDLEWARE AUTH & SESSION LOCK
Route::middleware(['auth', 'session.lock'])->group(function () {
    // ----------------- RUTE TAMPILAN (VIEWS) -----------------
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    Route::get('/surat-masuk', function () {
        if (auth()->user()->role === 'kabag') return redirect('/dashboard');
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
    // SECURE DOCUMENT SERVING
    // ==========================================
    Route::get('/arsip/dokumen/{filename}', [\App\Http\Controllers\FileController::class, 'show']);

    // ==========================================
    // ENDPOINT AJAX JQUERY DATA STREAM
    // ==========================================
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getSlaStats']);
    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::put('/api/users/{id}', [UserController::class, 'update']);
    Route::delete('/api/users/{id}', [UserController::class, 'destroy']);

    // Surat Masuk API Endpoints (Bebas Typo)
    Route::get('/api/surat-masuk', [SuratMasukController::class, 'index']);
    Route::post('/api/surat-masuk', [SuratMasukController::class, 'create']);
    Route::post('/api/surat-masuk/update/{id}', [SuratMasukController::class, 'updateDisposisi']);
    Route::post('/api/surat-masuk/parse', [SuratMasukController::class, 'parsePDF']);
    Route::get('/api/surat-masuk/export', [SuratMasukController::class, 'exportCsv']);
    Route::get('/api/surat-masuk/{id}/tindak-lanjut', [SuratMasukController::class, 'getTindakLanjut']);
    Route::post('/api/surat-masuk/{id}/tindak-lanjut', [SuratMasukController::class, 'storeTindakLanjut']);
    
    // Surat Keluar & Logs API Endpoints
    Route::get('/api/surat-keluar', [SuratKeluarController::class, 'index']);
    Route::post('/api/surat-keluar', [SuratKeluarController::class, 'create']);
    Route::get('/api/surat-keluar/export', [SuratKeluarController::class, 'exportCsv']);
    Route::get('/api/surat-keluar/today-signature', [SuratKeluarController::class, 'getTodaySignature']);
    Route::post('/api/surat-keluar/{id}/paraf', [SuratKeluarController::class, 'parafKabag']);
    Route::get('/api/logs', [SuratMasukController::class, 'getLogs']);
});