<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Method lama lu buat nampilin halaman blade dashboard
    public function index()
    {
        return view('dashboard');
    }

    // Method baru kita buat ngisap data statistik AJAX SLA pimpinan
    public function getSlaStats()
    {
        // 1. Hitung durasi batas SLA (3 hari yang lalu dari sekarang)
        $limitSla = Carbon::now()->subDays(3);

        // 2. Ambil metrik ringkas untuk statistik atas
        $totalSurat  = SuratMasuk::count();
        $totalPending = SuratMasuk::where('status', 'pending')->count();
        $totalDispo   = SuratMasuk::where('status', 'disposisi')->count();
        
        $totalSlaBreach = SuratMasuk::where('status', 'pending')
                            ->where('tanggal_masuk', '<=', $limitSla)
                            ->count();

        // 3. Ambil daftar surat darurat SLA
        $urgentSurat = SuratMasuk::where('status', 'pending')
                            ->where('tanggal_masuk', '<=', $limitSla)
                            ->orderBy('tanggal_masuk', 'asc')
                            ->take(5)
                            ->get()
                            ->map(function($surat) {
                                // PERBAIKAN: Gunakan (int) atau floor() biar angkanya bulat murni
                                $hariMandek = (int) Carbon::parse($surat->tanggal_masuk)->diffInDays(Carbon::now());
                                $surat->hari_mandek = $hariMandek;
                                return $surat;
                            });

        return response()->json([
            'status' => 200,
            'metrics' => [
                'total' => $totalSurat,
                'pending' => $totalPending,
                'disposisi' => $totalDispo,
                'sla_breach' => $totalSlaBreach
            ],
            'urgent_list' => $urgentSurat
        ], 200);
    }
}