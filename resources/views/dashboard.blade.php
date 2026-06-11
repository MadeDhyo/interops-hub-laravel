@extends('layout.main')

@section('title', 'Dashboard - InterOps-Hub')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-bold text-white tracking-wide">Dashboard Utama</h1>
        <p class="text-sm text-gray-400 mt-1">Ringkasan data metrik sistem dan audit trail InterOps-Hub</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg flex items-center justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Surat Masuk</p>
                <p class="text-3xl font-bold text-white">{{ $totalSuratMasuk }}</p>
            </div>
            <div class="bg-blue-500/10 text-blue-400 p-4 rounded-xl">
                <i class="fas fa-inbox text-2xl"></i>
            </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg flex items-center justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Surat Pending</p>
                <p class="text-3xl font-bold text-amber-400">{{ $suratPending }}</p>
            </div>
            <div class="bg-amber-500/10 text-amber-400 p-4 rounded-xl">
                <i class="fas fa-clock text-2xl"></i>
            </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg flex items-center justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Melebihi SLA (>= 3 Hari)</p>
                <p class="text-3xl font-bold text-red-400">{{ $suratSla }}</p>
            </div>
            <div class="bg-red-500/10 text-red-400 p-4 rounded-xl">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
        </div>

        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg flex items-center justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Surat Keluar</p>
                <p class="text-3xl font-bold text-emerald-400">{{ $totalSuratKeluar }}</p>
            </div>
            <div class="bg-emerald-500/10 text-emerald-400 p-4 rounded-xl">
                <i class="fas fa-paper-plane text-2xl"></i>
            </div>
        </div>

    </div>

    <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-bold text-white tracking-wide">Log Aktivitas Terbaru</h2>
                <p class="text-xs text-gray-400">5 riwayat aksi operator terakhir di dalam sistem</p>
            </div>
            <div class="text-indigo-400 text-sm">
                <i class="fas fa-history"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-700 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="pb-3 pt-2 pl-4">Aksi</th>
                        <th class="pb-3 pt-2">Rincian Perubahan</th>
                        <th class="pb-3 pt-2 pr-4 text-right">Waktu Sistem</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-700/50">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-gray-700/20 transition-colors duration-150">
                            <td class="py-3 pl-4 font-medium text-indigo-400">
                                <span class="bg-indigo-500/10 px-2.5 py-1 rounded-md text-xs">{{ $log->aksi }}</span>
                            </td>
                            <td class="py-3 text-gray-300">{{ $log->rincian }}</td>
                            <td class="py-3 pr-4 text-right text-gray-500 text-xs">
                                {{ $log->created_at ? $log->created_at->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-sm text-gray-500">
                                Belum ada rekam jejak aktivitas operator.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection