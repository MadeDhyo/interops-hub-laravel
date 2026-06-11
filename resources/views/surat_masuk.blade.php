@extends('layout.main')

@section('title', 'Surat Masuk - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide">Data Surat Masuk</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola dokumen surat masuk, tracking status, dan distribusi disposisi pimpinan</p>
        </div>
        <button onclick="openModal('modalTambah')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-indigo-600/20 flex items-center space-x-2 text-sm">
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Surat Masuk</span>
        </button>
    </div>

    <div class="bg-gray-800 p-5 rounded-2xl border border-gray-700 shadow-lg grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pencarian Smart</label>
            <input type="text" id="searchFilter" placeholder="Cari nomor, asal, perihal..." class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:border-indigo-500 text-sm">
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Mulai</label>
            <input type="date" id="startDateFilter" class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:border-indigo-500 text-sm">
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Selesai</label>
            <input type="date" id="endDateFilter" class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-xl text-gray-100 focus:outline-none focus:border-indigo-500 text-sm">
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
                        <th class="py-4 px-6">Asal Surat</th>
                        <th class="py-4 px-6">Perihal</th>
                        <th class="py-4 px-6">Tanggal Masuk</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="text-sm divide-y divide-gray-700/50">
                    </tbody>
            </table>
        </div>
        
        <div class="p-5 border-t border-gray-700 flex justify-between items-center bg-gray-800/30">
            <p id="paginationInfo" class="text-xs text-gray-400">Menampilkan halaman 1</p>
            <div class="flex space-x-2" id="paginationButtons">
                </div>
        </div>
    </div>
</div>

<div id="modalTambah" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden transform transition-all duration-300">
        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white">Input Surat Masuk Baru</h3>
            <button onclick="closeModal('modalTambah')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="formTambahSurat" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">No Surat</label>
                    <input type="text" name="no_surat" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Dari (Asal Surat)</label>
                <input type="text" name="dari" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Kepada (Tujuan)</label>
                <input type="text" name="kepada" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Perihal</label>
                <textarea name="perihal" rows="3" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500"></textarea>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Berkas Dokumen (PDF)</label>
                <input type="file" name="file_pdf" accept="application/pdf" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-600/10 file:text-indigo-400 hover:file:bg-indigo-600/20 cursor-pointer">
            </div>
            <div class="pt-4 flex justify-end space-x-3 border-t border-gray-700 mt-6">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl text-sm flex items-center space-x-2">
                    <span>Simpan Arsip</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalDisposisi" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white">Lembar Instruksi Disposisi</h3>
            <button onclick="closeModal('modalDisposisi')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="formDisposisi" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="disposisiSuratId">
            <div class="space-y-1 bg-gray-900/50 p-4 rounded-xl border border-gray-700/50 text-xs space-y-1">
                <p class="text-gray-400">Target Surat: <span id="textNoSurat" class="text-white font-semibold"></span></p>
                <p class="text-gray-400">Perihal: <span id="textPerihal" class="text-white"></span></p>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Nomor Agenda Disposisi</label>
                <input type="text" id="no_dispo" name="no_dispo" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Instruksi Kepala Bagian (Kabag)</label>
                <textarea id="disposisi_kabag" name="disposisi_kabag" rows = "2" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500" placeholder="Masukkan instruksi kabag..."></textarea>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Instruksi Kepala Sub-Bagian (Kasubag)</label>
                <textarea id="disposisi_kasubag" name="disposisi_kasubag" rows = "2" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500" placeholder="Masukkan instruksi kasubag..."></textarea>
            </div>
            <div class="pt-4 flex justify-end space-x-3 border-t border-gray-700 mt-6">
                <button type="button" onclick="closeModal('modalDisposisi')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm">Batal</button>
                <button type="submit" id="btnSubmitDisposisi" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl text-sm flex items-center space-x-2">
                    <i class="fas fa-paper-plane text-xs"></i>
                    <span>Kirim & Notif WA</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;

    $(document).ready(function() {
        // Daftarkan global interceptor CSRF token untuk setup jQuery Laravel
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Ambil data awal saat halaman siap
        fetchSuratMasuk(currentPage);

        // Submit Form Input Surat Masuk
        $('#formTambahSurat').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $('#btnSubmitTambah').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ url('/api/surat-masuk') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    closeModal('modalTambah');
                    $('#formTambahSurat')[0].reset();
                    Swal.fire({ icon: 'success', title: 'Sukses', text: res.message, background: '#1f2937', color: '#fff' });
                    fetchSuratMasuk(currentPage);
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan data.', background: '#1f2937', color: '#fff' });
                },
                complete: function() {
                    $('#btnSubmitTambah').prop('disabled', false).html('<span>Simpan Arsip</span>');
                }
            });
        });

        // Submit Form Pemberian Disposisi
        $('#formDisposisi').on('submit', function(e) {
            e.preventDefault();
            let id = $('#disposisiSuratId').val();
            let payload = {
                no_dispo: $('#no_dispo').val(),
                disposisi_kabag: $('#disposisi_kabag').val(),
                disposisi_kasubag: $('#disposisi_kasubag').val()
            };
            $('#btnSubmitDisposisi').prop('disabled', true).text('Mengirim...');

            $.ajax({
                url: "{{ url('/api/surat-masuk/update') }}/" + id,
                type: "POST",
                data: payload,
                success: function(res) {
                    closeModal('modalDisposisi');
                    Swal.fire({ icon: 'success', title: 'Disposisi Terkirim', text: res.message, background: '#1f2937', color: '#fff' });
                    fetchSuratMasuk(currentPage);
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memproses instruksi disposisi.', background: '#1f2937', color: '#fff' });
                },
                complete: function() {
                    $('#btnSubmitDisposisi').prop('disabled', false).html('<i class="fas fa-paper-plane text-xs"></i> <span>Kirim & Notif WA</span>');
                }
            });
        });
    });

    // Ambil Data dari REST API Laravel
    function fetchSuratMasuk(page) {
        currentPage = page;
        let queryParams = {
            page: page,
            search: $('#searchFilter').val(),
            start_date: $('#startDateFilter').val(),
            end_date: $('#endDateFilter').val()
        };

        $.ajax({
            url: "{{ url('/api/surat-masuk') }}",
            type: "GET",
            data: queryParams,
            dataType: "json",
            success: function(res) {
                renderTable(res.data);
                renderPagination(res.pagination);
            },
            error: function() {
                $('#tableBody').html('<tr><td colspan="6" class="text-center py-6 text-red-400">Gagal memuat log data surat masuk.</td></tr>');
            }
        });
    }

    // Render Rows ke Elemen Table Body
    function renderTable(data) {
        let html = '';
        if (data.length === 0) {
            html = '<tr><td colspan="6" class="text-center py-6 text-gray-500">Tidak ada arsip surat masuk ditemukan.</td></tr>';
            $('#tableBody').html(html);
            return;
        }

        data.forEach(row => {
            let badgeColor = row.status === 'pending' ? 'bg-amber-500/10 text-amber-400' : 'bg-emerald-500/10 text-emerald-400';
            let fileButton = row.file_pdf 
                ? `<a href="{{ url('/uploads') }}/${row.file_pdf}" target="_blank" class="text-indigo-400 hover:text-indigo-300 transition-colors"><i class="fas fa-file-pdf text-base"></i></a>` 
                : `<span class="text-gray-600">-</span>`;

            html += `
                <tr class="hover:bg-gray-700/20 transition-colors duration-150">
                    <td class="py-3.5 px-6 font-semibold text-white">${row.no_surat}</td>
                    <td class="py-3.5 px-6 text-gray-300">${row.dari}</td>
                    <td class="py-3.5 px-6 text-gray-300 max-w-xs truncate">${row.perihal}</td>
                    <td class="py-3.5 px-6 text-gray-400">${row.tanggal_masuk}</td>
                    <td class="py-3.5 px-6">
                        <span class="px-2.5 py-1 rounded-md text-xs font-medium capitalize ${badgeColor}">${row.status}</span>
                    </td>
                    <td class="py-3.5 px-6 text-center flex items-center justify-center space-x-4">
                        ${fileButton}
                        <button onclick="handleActionDisposisi(${row.id}, '${row.no_surat}', '${row.perihal}')" class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-xs rounded-lg text-gray-200 hover:text-white transition-all flex items-center space-x-1">
                            <i class="fas fa-share-square"></i>
                            <span>Disposisi</span>
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#tableBody').html(html);
    }

    // Render Kontrol Navigasi Halaman Pagination
    function renderPagination(meta) {
        $('#paginationInfo').text(`Halaman ${meta.page} dari ${meta.total_pages}`);
        let buttonsHtml = '';

        // Tombol Sebelumnya
        buttonsHtml += `<button onclick="fetchSuratMasuk(${meta.page - 1})" ${meta.page === 1 ? 'disabled' : ''} class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed text-xs rounded-lg font-medium text-white transition-all">Prev</button>`;
        
        // Tombol Selanjutnya
        buttonsHtml += `<button onclick="fetchSuratMasuk(${meta.page + 1})" ${meta.page === meta.total_pages || meta.total_pages === 0 ? 'disabled' : ''} class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed text-xs rounded-lg font-medium text-white transition-all">Next</button>`;

        $('#paginationButtons').html(buttonsHtml);
    }

    function handleFilter() {
        fetchSuratMasuk(1);
    }

    function handleActionDisposisi(id, noSurat, perihal) {
        $('#disposisiSuratId').val(id);
        $('#textNoSurat').text(noSurat);
        $('#textPerihal').text(perihal);
        openModal('modalDisposisi');
    }

    // Global Modal Helper Methods
    function openModal(id) {
        $(`#${id}`).removeClass('hidden');
    }

    function closeModal(id) {
        $(`#${id}`).addClass('hidden');
    }
</script>
@endpush