@extends('layout.main')

@section('title', 'Dashboard SLA Monitoring - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-white tracking-wide">Dashboard Pengawasan</h1>
        <p class="text-sm text-gray-400 mt-1">SLA Monitoring & Ringkasan Distribusi Komando Pimpinan</p>
    </div>

    <!-- Top Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <!-- Total Surat -->
        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl"><i class="fas fa-envelope text-2xl"></i></div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Surat Masuk</p>
                <h3 class="text-2xl font-bold text-white mt-1" id="metricTotal">0</h3>
            </div>
        </div>
        <!-- Pending -->
        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-amber-500/10 text-amber-400 rounded-xl"><i class="fas fa-clock text-2xl"></i></div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Review Pending</p>
                <h3 class="text-2xl font-bold text-white mt-1" id="metricPending">0</h3>
            </div>
        </div>
        <!-- Disposisi -->
        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl"><i class="fas fa-check-circle text-2xl"></i></div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sudah Disposisi</p>
                <h3 class="text-2xl font-bold text-white mt-1" id="metricDisposisi">0</h3>
            </div>
        </div>
        <!-- SLA Breach Warning -->
        <div class="bg-gray-800 p-6 rounded-2xl border border-red-500/30 bg-gradient-to-br from-gray-800 to-red-950/20 shadow-lg flex items-center space-x-4">
            <div class="p-3 bg-red-500/10 text-red-400 rounded-xl animate-pulse"><i class="fas fa-exclamation-triangle text-2xl"></i></div>
            <div>
                <p class="text-xs font-semibold text-red-400 uppercase tracking-wider">Lewat Batas SLA (>3 Hari)</p>
                <h3 class="text-2xl font-bold text-red-400 mt-1" id="metricSla">0</h3>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Section -->
        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg lg:col-span-1 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center space-x-2">
                    <i class="fas fa-chart-donut text-indigo-400"></i>
                    <span>Rasio Status Dokumen</span>
                </h3>
                <p class="text-xs text-gray-400 mt-1">Perbandingan berkas pending vs selesai didisposisikan</p>
            </div>
            <div class="py-4 flex justify-center items-center relative h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- SLA Warning Table Section -->
        <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 shadow-lg lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center">
                    <h3 class="text-base font-bold text-white flex items-center space-x-2">
                        <i class="fas fa-fire text-red-400"></i>
                        <span>Antrean Urgent (Darurat SLA)</span>
                    </h3>
                    <span class="px-2 py-0.5 bg-red-500/10 text-red-400 text-[10px] font-bold uppercase rounded border border-red-500/20">Butuh Aksi Segera</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Daftar surat masuk yang tertahan lebih dari 3 hari tanpa instruksi komando pimpinan.</p>
                
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-700 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="pb-3">No Surat</th>
                                <th class="pb-3">Asal</th>
                                <th class="pb-3 text-center">Keterlambatan</th>
                                {{-- Tombol Tindakan hanya tampil di header jika user adalah pimpinan --}}
                                @if(auth()->user()->role === 'pimpinan')
                                    <th class="pb-3 text-center" id="th-tindakan">Tindakan</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="urgentTableBody" class="text-xs divide-y divide-gray-700/40">
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-500">Memuat antrean darurat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-700/50 flex justify-end">
                <a href="{{ url('/surat-masuk') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center space-x-1">
                    <span>Buka Semua Surat Masuk</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let statusChartInstance = null;
    
    // Ambil data role user dari session backend Laravel
    const currentRole = "{{ auth()->user()->role }}";

    $(document).ready(function() {
        loadDashboardData();
    });

    function loadDashboardData() {
        $.ajax({
            url: "{{ url('/api/dashboard/stats') }}",
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status === 200) {
                    $('#metricTotal').text(res.metrics.total);
                    $('#metricPending').text(res.metrics.pending);
                    $('#metricDisposisi').text(res.metrics.disposisi);
                    $('#metricSla').text(res.metrics.sla_breach);

                    renderChart(res.metrics.pending_safe, res.metrics.sla_breach, res.metrics.disposisi);
                    renderUrgentTable(res.urgent_list);
                }
            }
        });
    }

    function renderChart(pendingSafe, slaBreach, disposisi) {
        const ctx = document.getElementById('statusChart').getContext('2d');
        
        if (statusChartInstance) {
            statusChartInstance.destroy();
        }

        statusChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending (<3 Hari)', 'Lewat SLA (>3 Hari)', 'Sudah Disposisi'],
                datasets: [{
                    data: [pendingSafe, slaBreach, disposisi],
                    backgroundColor: ['#fbbf24', '#f43f5e', '#10b981'],
                    borderColor: '#1f2937',
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            color: '#9ca3af', 
                            font: { size: 11, family: 'sans-serif' }, 
                            padding: 12 
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }

    function renderUrgentTable(list) {
        let html = '';
        const colspanValue = currentRole === 'pimpinan' ? 4 : 3;

        if (!list || list.length === 0) {
            html = `<tr><td colspan="${colspanValue}" class="text-center py-6 text-emerald-400 font-medium italic"><i class="fas fa-check-circle mr-1"></i> Aman! Semua berkas masuk di bawah batas waktu SLA.</td></tr>`;
            $('#urgentTableBody').html(html);
            return;
        }

        list.forEach(row => {
            let tdTindakan = '';
            
            // Render kolom tindakan HANYA jika usernya pimpinan
            if (currentRole === 'pimpinan') {
                // Perbaikan: Taruh encoder URL di dalam loop biar variabel 'row' terbaca sempurna
                let safeNoSurat = encodeURIComponent(row.no_surat);
                let safePerihalUrl = encodeURIComponent(row.perihal);

                tdTindakan = `
                    <td class="py-3 text-center">
                        <a href="{{ url('/surat-masuk') }}?autodispo=${row.id}&no_surat=${safeNoSurat}&perihal=${safePerihalUrl}" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-md text-[11px] transition-all inline-block shadow">
                            <i class="fas fa-file-signature mr-1"></i>Eksekusi
                        </a>
                    </td>
                `;
            }

            html += `
                <tr class="hover:bg-gray-700/20 transition-colors">
                    <td class="py-3 font-semibold text-white font-mono">${row.no_surat}</td>
                    <td class="py-3 text-gray-300">${row.dari}</td>
                    <td class="py-3 text-center">
                        <span class="px-2 py-0.5 bg-red-500/10 text-red-400 font-bold rounded border border-red-500/20 font-mono">
                            +${row.hari_mandek} Hari
                        </span>
                    </td>
                    ${tdTindakan}
                </tr>
            `;
        });
        $('#urgentTableBody').html(html);
    }
</script>
@endpush