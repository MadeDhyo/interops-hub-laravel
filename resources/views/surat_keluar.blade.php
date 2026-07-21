@extends('layout.main')

@section('title', 'Surat Keluar - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide">Data Surat Keluar</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola arsip surat keluar, distribusi instansi tujuan, dan tracking dokumen eksternal</p>
        </div>
        
        <div class="flex items-center space-x-2">
            <button onclick="exportCsv()" class="px-4 py-2.5 glass-card hover:bg-white/[0.05] border border-ops-border text-slate-300 font-medium rounded-lg transition-all duration-200 text-sm flex items-center space-x-2">
                <i class="fas fa-file-export text-xs"></i>
                <span>Export CSV</span>
            </button>
            @can('akses-admin')
            <button onclick="openModal('modalTambahKeluar')" class="px-5 py-2.5 btn-primary rounded-lg transition-all duration-200 shadow-lg flex items-center space-x-2 text-sm">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Surat Keluar</span>
            </button>
            @endcan
        </div>
    </div>

    <div class="glass-card p-5 rounded-xl grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pencarian Smart</label>
            <input type="text" id="searchFilter" placeholder="Cari nomor, tujuan, perihal..." class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none  text-sm">
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Mulai</label>
            <input type="date" id="startDateFilter" class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 focus:outline-none  text-sm">
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Selesai</label>
            <input type="date" id="endDateFilter" class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 focus:outline-none  text-sm">
        </div>
        <div>
            <button onclick="handleFilter()" class="w-full py-2.5 glass-card hover:bg-white/[0.05] border border-ops-border text-slate-300 font-medium rounded-lg transition-all duration-200 text-sm flex justify-center items-center space-x-2">
                <i class="fas fa-filter text-xs"></i>
                <span>Terapkan Filter</span>
            </button>
        </div>
    </div>

    <div class="glass-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-ops-border text-[10px] font-mono font-semibold uppercase tracking-widest text-slate-600 bg-ops-abyss/40">
                        <th class="py-4 px-6">No. Surat</th>
                        <th class="py-4 px-6">Ditujukan Ke</th>
                        <th class="py-4 px-6">Asal Pengirim</th>
                        <th class="py-4 px-6">Perihal</th>
                        <th class="py-4 px-6">Tanggal Surat</th>
                        <th class="py-4 px-6 text-center">Berkas</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="text-sm divide-y divide-ops-border">
                    <tr><td colspan="6"><div class="skeleton-row"><div class="skeleton-cell" style="width:15%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:25%"></div><div class="skeleton-cell" style="width:12%"></div><div class="skeleton-cell" style="width:8%"></div></div></td></tr>
                    <tr><td colspan="6"><div class="skeleton-row"><div class="skeleton-cell" style="width:18%"></div><div class="skeleton-cell" style="width:22%"></div><div class="skeleton-cell" style="width:18%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:14%"></div><div class="skeleton-cell" style="width:8%"></div></div></td></tr>
                    <tr><td colspan="6"><div class="skeleton-row"><div class="skeleton-cell" style="width:12%"></div><div class="skeleton-cell" style="width:25%"></div><div class="skeleton-cell" style="width:22%"></div><div class="skeleton-cell" style="width:18%"></div><div class="skeleton-cell" style="width:15%"></div><div class="skeleton-cell" style="width:8%"></div></div></td></tr>
                    <tr><td colspan="6"><div class="skeleton-row"><div class="skeleton-cell" style="width:16%"></div><div class="skeleton-cell" style="width:18%"></div><div class="skeleton-cell" style="width:24%"></div><div class="skeleton-cell" style="width:22%"></div><div class="skeleton-cell" style="width:12%"></div><div class="skeleton-cell" style="width:8%"></div></div></td></tr>
                    <tr><td colspan="6"><div class="skeleton-row"><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:15%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:24%"></div><div class="skeleton-cell" style="width:13%"></div><div class="skeleton-cell" style="width:8%"></div></div></td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="p-5 border-t border-ops-border flex justify-between items-center">
            <p id="paginationInfo" class="text-xs text-gray-400">Menampilkan halaman 1</p>
            <div class="flex space-x-2" id="paginationButtons"></div>
        </div>
    </div>
</div>

@can('akses-admin')
<div id="modalTambahKeluar" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden transform transition-all duration-300">
        <div class="px-6 py-4 border-b border-ops-border flex justify-between items-center">
            <h3 class="text-lg font-bold text-white">Input Surat Keluar Baru</h3>
            <button onclick="closeModal('modalTambahKeluar')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="formTambahSuratKeluar" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">No Surat</label>
                    <input type="text" name="no_surat" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none ">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Tanggal Surat</label>
                    <input type="date" name="tanggal_surat" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none ">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Dari (Pengirim)</label>
                    <input type="text" name="dari" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none " value="InterOps Hub Center">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Tanggal Input</label>
                    <input type="date" name="tanggal_input" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none " value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Kepada (Tujuan Instansi)</label>
                <input type="text" name="kepada" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none ">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Perihal</label>
                <textarea name="perihal" rows="3" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none " placeholder="Rincian perihal surat keluar..."></textarea>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Berkas Dokumen PDF (Opsional)</label>
                <input type="file" name="file_pdf" accept="application/pdf" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-ops-abyss file:text-ops-cyan hover:file:opacity-80 cursor-pointer">
            </div>
            <div class="pt-4 flex justify-end space-x-3 border-t border-ops-border mt-6">
                <button type="button" onclick="closeModal('modalTambahKeluar')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="px-4 py-2 btn-primary text-ops-abyss font-bold rounded-xl text-sm">
                    <span>Simpan Arsip</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let currentRole = "{{ auth()->user()->role }}";

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        fetchSuratKeluar(currentPage);

        // Hanya bind event submit jika formnya eksis (Admin Only)
        if ($('#formTambahSuratKeluar').length > 0) {
            $('#formTambahSuratKeluar').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $('#btnSubmitTambah').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ url('/api/surat-keluar') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        closeModal('modalTambahKeluar');
                        $('#formTambahSuratKeluar')[0].reset();
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, background: '#0b1628', color: '#fff', confirmButtonColor: '#00c6ff' });
                        fetchSuratKeluar(currentPage);
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memproses arsip surat keluar.', background: '#0b1628', color: '#fff', confirmButtonColor: '#ef4444' });
                    },
                    complete: function() {
                        $('#btnSubmitTambah').prop('disabled', false).html('<span>Simpan Arsip</span>');
                    }
                });
            });
        }
    });

    // Fetch surat keluar
    function fetchSuratKeluar(page) {
        currentPage = page;
        let filters = {
            page: page,
            search: $('#searchFilter').val(),
            start_date: $('#startDateFilter').val(),
            end_date: $('#endDateFilter').val()
        };

        $.ajax({
            url: "{{ url('/api/surat-keluar') }}",
            type: "GET",
            data: filters,
            dataType: "json",
            success: function(res) {
                renderTable(res.data);
                renderPagination(res.pagination);
            },
            error: function() {
                $('#tableBody').html('<tr><td colspan="6" class="text-center py-6 text-red-400">Gagal memuat log data surat keluar.</td></tr>');
            }
        });
    }

    function renderTable(data) {
        let html = '';
        if (!data || data.length === 0) {
            html = '<tr><td colspan="6" class="text-center py-6 text-gray-500">Tidak ada arsip surat keluar ditemukan.</td></tr>';
            $('#tableBody').html(html);
            return;
        }

        data.forEach(row => {
            let fileLink = row.file_pdf
                ? `<button type="button" onclick="openPdfModal('{{ url('/uploads') }}/${row.file_pdf}', '${row.file_pdf}')" class="text-ops-gold hover:text-white transition-colors" title="Preview PDF"><i class="fas fa-file-pdf text-lg"></i></button>`
                : `<span class="text-gray-600">-</span>`;

            html += `
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="py-3.5 px-6 font-semibold text-white font-mono text-xs">${row.no_surat}</td>
                    <td class="py-3.5 px-6 text-gray-300 text-xs">${row.kepada}</td>
                    <td class="py-3.5 px-6 text-gray-400 text-xs">${row.dari}</td>
                    <td class="py-3.5 px-6 text-gray-300 max-w-xs truncate text-xs" title="${row.perihal ? row.perihal.replace(/"/g, '&quot;') : ''}">${row.perihal}</td>
                    <td class="py-3.5 px-6 text-gray-400 font-mono text-xs">${row.tanggal_surat}</td>
                    <td class="py-3.5 px-6 text-center">${fileLink}</td>
                </tr>
            `;
        });
        $('#tableBody').html(html);
    }

    function renderPagination(meta) {
        $('#paginationInfo').text(`Halaman ${meta.page} dari ${meta.total_pages}`);
        let buttonsHtml = '';
        buttonsHtml += `<button onclick="fetchSuratKeluar(${meta.page - 1})" ${meta.page === 1 ? 'disabled' : ''} class="px-3 py-1.5 glass-card border-ops-border hover:bg-white/[0.05] disabled:opacity-40 disabled:cursor-not-allowed text-xs rounded-lg font-medium text-white transition-all">Prev</button>`;
        buttonsHtml += `<button onclick="fetchSuratKeluar(${meta.page + 1})" ${meta.page === meta.total_pages || meta.total_pages === 0 ? 'disabled' : ''} class="px-3 py-1.5 glass-card border-ops-border hover:bg-white/[0.05] disabled:opacity-40 disabled:cursor-not-allowed text-xs rounded-lg font-medium text-white transition-all">Next</button>`;
        $('#paginationButtons').html(buttonsHtml);
    }

    function handleFilter() { fetchSuratKeluar(1); }
    function openModal(id) { $(`#${id}`).removeClass('hidden'); }
    function closeModal(id) { $(`#${id}`).addClass('hidden'); }

    function exportCsv() {
        let params = new URLSearchParams({
            search: $('#searchFilter').val() || '',
            start_date: $('#startDateFilter').val() || '',
            end_date: $('#endDateFilter').val() || ''
        });
        window.location.href = "{{ url('/api/surat-keluar/export') }}?" + params.toString();
    }
</script>
@endpush