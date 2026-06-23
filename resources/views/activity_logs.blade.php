@extends('layout.main')

@section('title', 'Log Aktivitas Sistem - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white tracking-wide">Log Aktivitas Sistem</h1>
        <p class="text-sm text-gray-400 mt-1">Audit Trail & Rekaman jejak operasional sistem manajemen kearsipan InterOps-Hub</p>
    </div>

    <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-lg overflow-hidden">
        <div class="p-5 border-b border-gray-700 flex justify-between items-center bg-gray-800/50">
            <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider">Rekaman Riwayat Operasional</h3>
            <button onclick="fetchActivityLogs()" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 hover:text-white rounded-xl text-xs font-semibold transition-all flex items-center space-x-1">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh Log</span>
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-700 text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-800/30">
                        <th class="py-4 px-6 w-20">No</th>
                        <th class="py-4 px-6 w-52">Waktu Kejadian</th>
                        <th class="py-4 px-6 w-52">Aksi / Kegiatan</th>
                        <th class="py-4 px-6">Rincian Deskripsi</th>
                    </tr>
                </thead>
                <tbody id="logTableBody" class="text-sm divide-y divide-gray-700/50">
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">
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
    $(document).ready(function() {
        // Otomatis load log saat halaman dibuka
        fetchActivityLogs();
    });

    function fetchActivityLogs() {
        $('#logTableBody').html('<tr><td colspan="4" class="text-center py-8 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i> Mengambil data audit terupdate...</td></tr>');

        $.ajax({
            url: "{{ url('/api/logs') }}",
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
            html = '<tr><td colspan="4" class="text-center py-8 text-gray-500">Belum ada rekaman log aktivitas terekam dalam sistem.</td></tr>';
            $('#logTableBody').html(html);
            return;
        }

        data.forEach((row, index) => {
            // Berikan badge warna berdasarkan jenis aksinya agar gampang dibaca auditor
            let badgeColor = 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20';
            if (row.aksi.includes('Input')) {
                badgeColor = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
            } else if (row.aksi.includes('Disposisi')) {
                badgeColor = 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
            } else if (row.aksi.includes('Hapus') || row.aksi.includes('Tolak')) {
                badgeColor = 'bg-rose-500/10 text-rose-400 border border-rose-500/20';
            }

            // Memformat string tanggal bawaan laravel (created_at) biar makin clean
            let waktu = row.created_at ? new Date(row.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : '-';

            html += `
                <tr class="hover:bg-gray-700/10 transition-colors duration-150">
                    <td class="py-4 px-6 text-gray-500 font-mono text-xs">${index + 1}</td>
                    <td class="py-4 px-6 text-gray-400 font-mono text-xs">${waktu}</td>
                    <td class="py-4 px-6">
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold ${badgeColor}">
                            ${row.aksi}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-gray-300 font-medium">${row.rincian}</td>
                </tr>
            `;
        });
        $('#logTableBody').html(html);
    }

    function renderErrorTable() {
        $('#logTableBody').html('<tr><td colspan="4" class="text-center py-8 text-rose-400"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal berkomunikasi dengan layanan core audit log.</td></tr>');
    }
</script>
@endpush