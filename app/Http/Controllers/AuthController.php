<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function index()
    {
        // If user is already logged in, send them straight to dashboard
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login'); // Points to resources/views/auth/login.blade.php
    }

    public function attemptLogin(Request $request)
    {
        // Validate inputs
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $throttleKey = \Illuminate\Support\Str::lower($request->input('username')) . '|' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status' => 429,
                'message' => "Terlalu banyak percobaan. Coba lagi dalam $seconds detik."
            ], 429);
        }

        // Attempt login session creation
        if (Auth::attempt($credentials)) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Log activity trail
            ActivityLog::create([
                'aksi' => 'Login',
                'rincian' => 'User ' . Auth::user()->username . ' berhasil login ke sistem.'
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Login sukses',
                'redirect' => url('/dashboard')
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        return response()->json([
            'status' => 401,
            'message' => 'Username atau password salah.'
        ], 401);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'aksi' => 'Logout',
                'rincian' => 'User ' . Auth::user()->username . ' keluar dari sistem.'
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}