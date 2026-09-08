@extends('layout.main')

@section('title', 'Log Aktivitas Sistem - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide">Log Aktivitas Sistem</h1>
            <p class="text-sm text-gray-400 mt-1">Audit Trail &amp; Rekaman jejak mutasi dokumen per subbag InterOps-Hub</p>
        </div>
        <button onclick="fetchActivityLogs()" class="px-4 py-2 glass-card hover:bg-white/[0.05] border border-ops-border text-slate-300 hover:text-white rounded-lg text-xs font-semibold transition-all flex items-center space-x-1.5 shadow-sm">
            <i class="fas fa-sync-alt"></i>
            <span>Refresh Audit Log</span>
        </button>
    </div>

    <!-- Filter & Pencarian -->
    <div class="glass-card px-5 py-4 rounded-xl grid grid-cols-1 md:grid-cols-2 gap-4 items-center border border-ops-border/30">
        <div>
            <span class="section-eyebrow mb-2 block">Filter Subbag:</span>
            <div class="flex flex-wrap gap-2">
                <button onclick="setLogSubbagFilter(null)" id="fLogAll" class="log-filter-btn px-3 py-1 rounded-md text-xs font-semibold transition-all bg-ops-cyan/20 border border-ops-cyan/40 text-ops-cyan">Semua</button>
                @php
                    $subbags = ['urmin','bhi','bi','ops','koor'];
                    if(auth()->user()->role === 'admin') {
                        array_unshift($subbags, 'kabag');
                    }
                @endphp
                @foreach($subbags as $sb)
                <button onclick="setLogSubbagFilter('{{ $sb }}')" id="fLog{{ $sb }}" class="log-filter-btn px-3 py-1 rounded-md text-xs font-semibold transition-all border border-ops-border/40 text-slate-400 hover:border-ops-cyan/40 hover:text-ops-cyan">{{ strtoupper($sb) }}</button>
                @endforeach
            </div>
        </div>
        <div>
            <span class="section-eyebrow mb-2 block">Pencarian Smart:</span>
            <div class="flex gap-2">
                <input type="text" id="searchLog" placeholder="Cari aksi, rincian, atau subbag..." class="w-full px-4 py-2 ops-input rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none text-sm">
                <button onclick="fetchActivityLogs()" class="px-4 py-2 bg-ops-cyan text-slate-900 rounded-lg text-sm font-semibold hover:bg-ops-cyan/80 transition-colors">Cari</button>
            </div>
        </div>
    </div>

    <div class="glass-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-ops-border text-[10px] font-mono font-semibold uppercase tracking-widest text-slate-600 bg-ops-abyss/30">
                        <th class="py-4 px-6 w-16">No</th>
                        <th class="py-4 px-6 w-44">Waktu Kejadian</th>
                        <th class="py-4 px-6 w-28">Subbag</th>
                        <th class="py-4 px-6 w-48">Aksi / Kegiatan</th>
                        <th class="py-4 px-6">Rincian Deskripsi</th>
                    </tr>
                </thead>
                <tbody id="logTableBody" class="text-sm divide-y divide-ops-border">
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500 font-mono text-xs">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Menghubungkan ke server audit trail...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentLogSubbag = null;

    $(document).ready(function() {
        fetchActivityLogs();

        $('#searchLog').on('keyup', function(e) {
            if(e.key === 'Enter') {
                fetchActivityLogs();
            }
        });
    });

    function setLogSubbagFilter(subbag) {
        currentLogSubbag = subbag;
        $('.log-filter-btn').removeClass('bg-ops-cyan/20 border-ops-cyan/40 text-ops-cyan').addClass('border-ops-border/40 text-slate-400');
        if (subbag === null) {
            $('#fLogAll').addClass('bg-ops-cyan/20 border-ops-cyan/40 text-ops-cyan').removeClass('border-ops-border/40 text-slate-400');
        } else {
            $('#fLog' + subbag).addClass('bg-ops-cyan/20 border-ops-cyan/40 text-ops-cyan').removeClass('border-ops-border/40 text-slate-400');
        }
        fetchActivityLogs();
    }

    function fetchActivityLogs() {
        $('#logTableBody').html('<tr><td colspan="5" class="text-center py-8 text-gray-500 font-mono text-xs"><i class="fas fa-spinner fa-spin mr-2"></i> Mengambil data audit terupdate...</td></tr>');

        let url = "{{ url('/api/logs') }}";
        let params = [];
        
        if (currentLogSubbag) {
            params.push(`subbag=${encodeURIComponent(currentLogSubbag)}`);
        }
        
        let search = $('#searchLog').val();
        if (search) {
            params.push(`search=${encodeURIComponent(search)}`);
        }

        if(params.length > 0) {
            url += '?' + params.join('&');
        }

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status === 200) {
                    renderLogTable(res.data);
                } else {
                    renderErrorTable();
                }
            },
            error: function() {
                renderErrorTable();
            }
        });
    }

    function renderLogTable(data) {
        let html = '';
        if (!data || data.length === 0) {
            html = `<tr>
                <td colspan="5" class="text-center py-12 text-slate-400 font-mono text-xs">
                    <div class="flex flex-col items-center justify-center space-y-2">
                        <i class="fas fa-search text-2xl text-slate-600"></i>
                        <p class="font-semibold text-slate-400">Tidak ada log aktivitas ditemukan</p>
                        <p class="text-[11px] text-slate-500">Coba ubah kata kunci pencarian atau reset filter subbag</p>
                    </div>
                </td>
            </tr>`;
            $('#logTableBody').html(html);
            return;
        }

        data.forEach((row, index) => {
            let badgeClass = 'stamp stamp-disposisi';
            if (row.aksi && (row.aksi.includes('Hapus') || row.aksi.includes('Tolak'))) {
                badgeClass = 'stamp stamp-red';
            } else if (row.aksi && (row.aksi.includes('Paraf') || row.aksi.includes('Disposisi'))) {
                badgeClass = 'stamp stamp-pending';
            }

            let waktu = row.created_at ? new Date(row.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : '-';
            let safeSubbag = row.subbag ? escapeHtml(row.subbag) : '';
            let subbagBadge = safeSubbag
                ? `<span class="px-2 py-0.5 rounded bg-ops-cyan/10 border border-ops-cyan/30 text-ops-cyan font-mono text-[10px] uppercase">${safeSubbag}</span>`
                : '<span class="text-slate-500 italic text-xs">Global</span>';

            let safeAksi = escapeHtml(row.aksi);
            let safeRincian = escapeHtml(row.rincian);

            html += `
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="py-4 px-6 text-gray-500 font-mono text-xs">${index + 1}</td>
                    <td class="py-4 px-6 text-gray-400 font-mono text-xs">${waktu}</td>
                    <td class="py-4 px-6">${subbagBadge}</td>
                    <td class="py-4 px-6">
                        <span class="${badgeClass} text-[10px]">
                            ${safeAksi}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-gray-300 font-medium text-xs">${safeRincian}</td>
                </tr>
            `;
        });
        $('#logTableBody').html(html);
    }

    function renderErrorTable() {
        $('#logTableBody').html('<tr><td colspan="5" class="text-center py-8 text-rose-400 font-mono text-xs"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal berkomunikasi dengan layanan core audit log.</td></tr>');
    }
</script>
@endpush