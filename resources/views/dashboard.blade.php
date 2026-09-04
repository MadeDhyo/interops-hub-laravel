@extends('layout.main')

@section('title', 'Dashboard SLA Monitoring - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="section-eyebrow mb-1">// SLA MONITORING · DISTRIBUSI KOMANDO</p>
            <h1 class="font-display text-3xl text-white">Dashboard Pengawasan</h1>
            <p class="text-sm text-slate-500 mt-1">Ringkasan operasional &amp; status ketaatan batas waktu disposisi pimpinan</p>
        </div>
        <button onclick="openSopModal()" class="px-4 py-2 rounded-xl bg-ops-gold/15 hover:bg-ops-gold/25 border border-ops-gold/40 text-ops-gold text-xs font-bold transition-all flex items-center space-x-2 self-start sm:self-auto shadow-sm">
            <i class="fas fa-book-reader"></i>
            <span>Buku Saku SOP Peran</span>
        </button>
    </div>

    <!-- Role Context Banner (Do's & Don'ts) -->
    @include('components.role_banner')

    <!-- Metrics Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="glass-card bracket-box p-5 rounded-xl flex items-center space-x-4 fade-in-up fade-delay-1">
            <div class="p-2.5 rounded-lg" style="background: rgba(0,198,255,0.08);">
                <i class="fas fa-envelope text-xl" style="color: #00c6ff;"></i>
            </div>
            <div>
                <p class="section-eyebrow">Total Surat Masuk</p>
                <h3 class="font-mono text-2xl font-semibold text-white mt-0.5" id="metricTotal">—</h3>
            </div>
        </div>
        <!-- Pending -->
        <div class="glass-card bracket-box p-5 rounded-xl flex items-center space-x-4 fade-in-up fade-delay-2">
            <div class="p-2.5 rounded-lg" style="background: rgba(245,158,11,0.08);">
                <i class="fas fa-clock text-xl" style="color: #f59e0b;"></i>
            </div>
            <div>
                <p class="section-eyebrow" style="color: rgba(245,158,11,0.6);">Review Pending</p>
                <h3 class="font-mono text-2xl font-semibold text-white mt-0.5" id="metricPending">—</h3>
            </div>
        </div>
        <!-- Disposisi -->
        <div class="glass-card bracket-box p-5 rounded-xl flex items-center space-x-4 fade-in-up fade-delay-3">
            <div class="p-2.5 rounded-lg" style="background: rgba(167,139,250,0.08);">
                <i class="fas fa-check-circle text-xl" style="color: #a78bfa;"></i>
            </div>
            <div>
                <p class="section-eyebrow" style="color: rgba(167,139,250,0.6);">Sudah Disposisi</p>
                <h3 class="font-mono text-2xl font-semibold text-white mt-0.5" id="metricDisposisi">—</h3>
            </div>
        </div>
        <!-- SLA Breach -->
        <div class="bracket-box p-5 rounded-xl flex items-center space-x-4 fade-in-up fade-delay-4" style="background: rgba(255,51,51,0.06); border: 1px solid rgba(255,51,51,0.2); backdrop-filter: blur(16px);">
            <div class="p-2.5 rounded-lg animate-pulse" style="background: rgba(255,51,51,0.1);">
                <i class="fas fa-exclamation-triangle text-xl" style="color: #ff3333;"></i>
            </div>
            <div>
                <p class="section-eyebrow" style="color: rgba(255,51,51,0.7);">Lewat SLA (&gt;3 Hari)</p>
                <h3 class="font-mono text-2xl font-semibold mt-0.5" style="color: #ff3333;" id="metricSla">—</h3>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Chart -->
        <div class="glass-card p-6 rounded-xl lg:col-span-1 flex flex-col fade-in-up fade-delay-5">
            <div>
                <p class="section-eyebrow mb-1">Distribusi Status</p>
                <h3 class="font-display text-base text-white">Rasio Dokumen</h3>
                <p class="text-xs text-slate-500 mt-1">Berkas pending vs. terdisposisi</p>
            </div>
            <div class="flex-1 flex justify-center items-center py-4 min-h-[220px]">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Urgent Table -->
        <div class="glass-card p-6 rounded-xl lg:col-span-2 flex flex-col fade-in-up fade-delay-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="section-eyebrow mb-1" style="color: rgba(255,51,51,0.6);">Antrean Prioritas Tinggi</p>
                    <h3 class="font-display text-base text-white">Darurat SLA</h3>
                    <p class="text-xs text-slate-500 mt-1">Tertahan &gt;3 hari tanpa instruksi pimpinan</p>
                </div>
                <span class="stamp stamp-red mt-1">Butuh Aksi</span>
            </div>

            <div class="overflow-x-auto mt-5 flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-mono font-semibold uppercase tracking-widest text-slate-600 border-b border-ops-border">
                            <th class="pb-3 pl-3">No Surat</th>
                            <th class="pb-3">Asal</th>
                            <th class="pb-3 text-center">Keterlambatan</th>
                            @can('bisa-disposisi')
                                <th class="pb-3 text-center" id="th-tindakan">Tindakan</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody id="urgentTableBody" class="text-xs divide-y divide-ops-border">
                        <tr><td colspan="4"><div class="skeleton-row"><div class="skeleton-cell" style="width:25%"></div><div class="skeleton-cell" style="width:35%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:20%"></div></div></td></tr>
                        <tr><td colspan="4"><div class="skeleton-row"><div class="skeleton-cell" style="width:30%"></div><div class="skeleton-cell" style="width:25%"></div><div class="skeleton-cell" style="width:25%"></div><div class="skeleton-cell" style="width:20%"></div></div></td></tr>
                        <tr><td colspan="4"><div class="skeleton-row"><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:40%"></div><div class="skeleton-cell" style="width:15%"></div><div class="skeleton-cell" style="width:25%"></div></div></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-ops-border flex justify-end mt-4">
                <a href="{{ url('/surat-masuk') }}" class="font-mono text-xs font-semibold transition-colors flex items-center space-x-1" style="color: #00c6ff;">
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
    const canDisposisi = {{ auth()->user()->canDisposisi() ? 'true' : 'false' }};
    const currentRole = "{{ auth()->user()->role }}";
    const currentSubbag = "{{ auth()->user()->subbag }}";

    $(document).ready(function() { loadDashboardData(); });

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
        if (statusChartInstance) statusChartInstance.destroy();

        statusChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending (<3 Hari)', 'Lewat SLA (>3 Hari)', 'Sudah Disposisi'],
                datasets: [{
                    data: [pendingSafe, slaBreach, disposisi],
                    backgroundColor: ['#f59e0b', '#ff3333', '#00c6ff'],
                    borderColor: 'rgba(11,22,40,0.8)',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#64748b',
                            font: { size: 10, family: 'IBM Plex Mono' },
                            padding: 14,
                            usePointStyle: true,
                            pointStyleWidth: 8
                        }
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function() { return '(Klik untuk filter)'; }
                        }
                    }
                },
                cutout: '72%',
                onClick: function(evt, elements) {
                    if (elements.length > 0) {
                        const idx = elements[0].index;
                        const statusMap = ['pending', 'sla', 'disposisi'];
                        window.location.href = "{{ url('/surat-masuk') }}?status=" + statusMap[idx];
                    }
                }
            }
        });
    }

    function renderUrgentTable(list) {
        let html = '';
        const colspanValue = canDisposisi ? 4 : 3;

        if (!list || list.length === 0) {
            html = `<tr><td colspan="${colspanValue}" class="text-center py-8 font-mono text-xs italic" style="color: #00c6ff;"><i class="fas fa-check-circle mr-2"></i>Aman — semua berkas di bawah batas SLA.</td></tr>`;
            $('#urgentTableBody').html(html);
            return;
        }

        list.forEach(row => {
            let tdTindakan = '';
            if (canDisposisi) {
                let isAllowedToDispoRow = false;
                if (currentRole === 'admin' || currentRole === 'kabag') {
                    isAllowedToDispoRow = true;
                } else if (currentRole === 'kasubbag') {
                    if (row.subbags && row.subbags.some(s => s.subbag === currentSubbag)) {
                        isAllowedToDispoRow = true;
                    }
                }

                if (isAllowedToDispoRow) {
                    let safeNoSurat = encodeURIComponent(row.no_surat);
                    let safePerihalUrl = encodeURIComponent(row.perihal);
                    tdTindakan = `
                        <td class="py-3 text-center">
                            <a href="{{ url('/surat-masuk') }}?autodispo=${row.id}&no_surat=${safeNoSurat}&perihal=${safePerihalUrl}" class="btn-primary px-3 py-1.5 rounded-md text-[11px] inline-flex items-center space-x-1">
                                <i class="fas fa-file-signature"></i><span>Eksekusi Disposisi</span>
                            </a>
                        </td>
                    `;
                } else {
                    tdTindakan = `
                        <td class="py-3 text-center">
                            <span class="text-xs text-slate-500 italic">Hanya Memantau</span>
                        </td>
                    `;
                }
            }

            html += `
                <tr class="hover:bg-white/[0.02] transition-colors" style="border-left: 2px solid rgba(255,51,51,0.5);">
                    <td class="py-3 pl-3 font-mono text-xs font-semibold text-white">${row.no_surat}</td>
                    <td class="py-3 text-sm text-slate-400">${row.dari}</td>
                    <td class="py-3 text-center">
                        <span class="stamp stamp-red">+${row.hari_mandek} Hari</span>
                    </td>
                    ${tdTindakan}
                </tr>
            `;
        });
        $('#urgentTableBody').html(html);
    }
</script>
@endpush