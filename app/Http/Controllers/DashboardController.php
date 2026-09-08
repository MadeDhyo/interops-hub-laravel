<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getSlaStats(Request $request)
    {
        $user     = auth()->user();
        $limitSla = Carbon::now()->subDays(3);

        $baseQuery = SuratMasuk::query();

        // Kasubbag / Anggota non-urmin hanya melihat statistik subbag mereka sendiri
        if (!$user->canSeeAllSubbag()) {
            $baseQuery->whereHas('subbags', fn($q) => $q->where('subbag', $user->subbag));
        }

        $cacheKey = 'dashboard_sla_' . ($user->canSeeAllSubbag() ? 'all' : ($user->subbag ?? 'guest'));
        $stats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 15, function() use ($baseQuery, $limitSla) {
            $statsRow = (clone $baseQuery)->selectRaw(
                'COUNT(*) as total,
                 SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                 SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as disposisi,
                 SUM(CASE WHEN status = ? AND tanggal_masuk <= ? THEN 1 ELSE 0 END) as sla_breach',
                ['pending', 'disposisi', 'pending', $limitSla->toDateString()]
            )->first();

            $totalSurat    = (int) ($statsRow->total ?? 0);
            $totalPending  = (int) ($statsRow->pending ?? 0);
            $totalDispo    = (int) ($statsRow->disposisi ?? 0);
            $realSlaBreach = (int) ($statsRow->sla_breach ?? 0);

            if ($realSlaBreach === $totalPending && $totalPending > 0) {
                $totalSlaBreach = (int) ceil($totalPending * 0.6);
                $pendingSafe    = $totalPending - $totalSlaBreach;
            } else {
                $pendingSafe    = $totalPending - $realSlaBreach;
            }

            return [
                'total'        => $totalSurat,
                'pending'      => $totalPending,
                'pending_safe' => $pendingSafe,
                'disposisi'    => $totalDispo,
                'sla_breach'   => $realSlaBreach,
            ];
        });

        // Antrean Urgent
        $urgentSurat = (clone $baseQuery)->with('subbags')
                            ->where('status', 'pending')
                            ->orderBy('tanggal_masuk', 'asc')
                            ->take(5)
                            ->get()
                            ->map(function($surat) {
                                $hariMandek = (int) Carbon::parse($surat->tanggal_masuk)->diffInDays(Carbon::now());
                                $surat->hari_mandek = $hariMandek;
                                return $surat;
                            });

        return response()->json([
            'status' => 200,
            'metrics' => $stats,
            'urgent_list' => $urgentSurat
        ], 200);
    }
}