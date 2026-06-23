@extends('layout.main')

@section('title', 'Surat Keluar - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide">Data Surat Keluar</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola arsip surat keluar, distribusi instansi tujuan, dan tracking dokumen eksternal</p>
        </div>
        
        @can('akses-admin')
        <button onclick="openModal('modalTambahKeluar')" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-emerald-600/20 flex items-center space-x-2 text-sm">
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Surat Keluar</span>
        </button>
        @endcan
    </div>

    <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 shadow-lg grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pencarian Smart</label>
            <input type="text" id="searchFilter" placeholder="Cari nomor, tujuan, perihal..." class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:border-emerald-500 text-sm">
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Mulai</label>
            <input type="date" id="startDateFilter" class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:border-emerald-500 text-sm">
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Selesai</label>
            <input type="date" id="endDateFilter" class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:border-emerald-500 text-sm">
        </div>
        <div>
            <button onclick="handleFilter()" class="w-full py-2.5 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl transition-all duration-200 text-sm flex justify-center items-center space-x-2">
                <i class="fas fa-filter text-xs"></i>
                <span>Terapkan Filter</span>
            </button>
        </div>
    </div>

    <div class="bg-gray-800 rounded-2xl border border-gray-700 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-700 text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-800/50">
                        <th class="py-4 px-6">No. Surat</th>
                        <th class="py-4 px-6">Ditujukan Ke</th>
                        <th class="py-4 px-6">Asal Pengirim</th>
                        <th class="py-4 px-6">Perihal</th>
                        <th class="py-4 px-6">Tanggal Surat</th>
                        <th class="py-4 px-6 text-center">Berkas</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="text-sm divide-y divide-gray-700/50">
                    </tbody>
            </table>
        </div>
        
        <div class="p-5 border-t border-gray-700 flex justify-between items-center bg-gray-800/30">
            <p id="paginationInfo" class="text-xs text-gray-400">Menampilkan halaman 1</p>
            <div class="flex space-x-2" id="paginationButtons"></div>
        </div>
    </div>
</div>

@can('akses-admin')
<div id="modalTambahKeluar" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden transform transition-all duration-300">
        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white">Input Surat Keluar Baru</h3>
            <button onclick="closeModal('modalTambahKeluar')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="formTambahSuratKeluar" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">No Surat</label>
                    <input type="text" name="no_surat" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Tanggal Surat</label>
                    <input type="date" name="tanggal_surat" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Dari (Pengirim)</label>
                    <input type="text" name="dari" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500" value="InterOps Hub Center">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Tanggal Input</label>
                    <input type="date" name="tanggal_input" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Kepada (Tujuan Instansi)</label>
                <input type="text" name="kepada" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Perihal</label>
                <textarea name="perihal" rows="3" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500" placeholder="Rincian perihal surat keluar..."></textarea>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Berkas Dokumen PDF (Opsional)</label>
                <input type="file" name="file_pdf" accept="application/pdf" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-600/10 file:text-emerald-400 hover:file:bg-emerald-600/20 cursor-pointer">
            </div>
            <div class="pt-4 flex justify-end space-x-3 border-t border-gray-700 mt-6">
                <button type="button" onclick="closeModal('modalTambahKeluar')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl text-sm">
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
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, background: '#1f2937', color: '#fff' });
                        fetchSuratKeluar(currentPage);
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memproses arsip surat keluar.', background: '#1f2937', color: '#fff' });
                    },
                    complete: function() {
                        $('#btnSubmitTambah').prop('disabled', false).html('<span>Simpan Arsip</span>');
                    }
                });
            });
        }
    });

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
                ? `<a href="{{ url('/uploads') }}/${row.file_pdf}" target="_blank" class="text-emerald-400 hover:text-emerald-300 transition-colors" title="Lihat PDF"><i class="fas fa-file-pdf text-lg"></i></a>` 
                : `<span class="text-gray-600">-</span>`;

            html += `
                <tr class="hover:bg-gray-700/20 transition-colors duration-150">
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
        buttonsHtml += `<button onclick="fetchSuratKeluar(${meta.page - 1})" ${meta.page === 1 ? 'disabled' : ''} class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed text-xs rounded-lg font-medium text-white transition-all">Prev</button>`;
        buttonsHtml += `<button onclick="fetchSuratKeluar(${meta.page + 1})" ${meta.page === meta.total_pages || meta.total_pages === 0 ? 'disabled' : ''} class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed text-xs rounded-lg font-medium text-white transition-all">Next</button>`;
        $('#paginationButtons').html(buttonsHtml);
    }

    function handleFilter() { fetchSuratKeluar(1); }
    function openModal(id) { $(`#${id}`).removeClass('hidden'); }
    function closeModal(id) { $(`#${id}`).addClass('hidden'); }
</script>
@endpush