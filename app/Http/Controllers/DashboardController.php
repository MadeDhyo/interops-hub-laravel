<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use App\Models\ActivityLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung statistik untuk metric cards
        $totalSuratMasuk = SuratMasuk::count();
        $suratPending = SuratMasuk::where('status', 'pending')->count();
        
        // SLA: Status masih pending dan tanggal_masuk sudah 3 hari atau lebih yang lalu
        $suratSla = SuratMasuk::where('status', 'pending')
            ->where('tanggal_masuk', '<=', Carbon::now()->subDays(3))
            ->count();
            
        $totalSuratKeluar = SuratKeluar::count();

        // 2. Ambil 5 log aktivitas terbaru untuk komponen Audit Trail
        $recentLogs = ActivityLog::orderBy('id', 'desc')->take(5)->get();

        // 3. Lempar semua data ke view resources/views/dashboard.blade.php
        return view('dashboard', compact(
            'totalSuratMasuk', 
            'suratPending', 
            'suratSla', 
            'totalSuratKeluar', 
            'recentLogs'
        ));
    }
}