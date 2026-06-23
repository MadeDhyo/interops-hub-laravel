@extends('layout.main')

@section('title', 'Surat Masuk - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide">Data Surat Masuk</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola dokumen surat masuk, tracking status, dan distribusi disposisi pimpinan</p>
        </div>
        
        @can('akses-admin')
        <button onclick="openModal('modalTambah')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-indigo-600/20 flex items-center space-x-2 text-sm">
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Surat Masuk</span>
        </button>
        @endcan
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
            <div class="flex space-x-2" id="paginationButtons"></div>
        </div>
    </div>
</div>

@can('akses-admin')
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
                <div class="flex gap-2 items-center">
                    <input type="file" id="file_pdf_input" name="file_pdf" accept="application/pdf" class="flex-1 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-600/10 file:text-indigo-400 hover:file:bg-indigo-600/20 cursor-pointer">
                    <button type="button" onclick="triggerAutoScan()" id="btnAutoScan" class="px-4 py-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-400 font-semibold rounded-xl text-xs transition-all flex items-center space-x-1 whitespace-nowrap">
                        <i class="fas fa-robot"></i>
                        <span>Auto Scan</span>
                    </button>
                </div>
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
@endcan

<div id="disposisiModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 border border-gray-700 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="dispoModalContent">
        <div class="p-6 border-b border-gray-700 flex justify-between items-center bg-gray-800/50">
            <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                <i class="fas fa-gavel text-amber-400"></i>
                <span>Lembar Disposisi Digital</span>
            </h3>
            <button onclick="closeDisposisiModal()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="disposisiForm" class="p-6 space-y-4">
            <input type="hidden" id="dispoSuratId" name="surat_id">
            
            <div class="space-y-1 bg-gray-900/50 p-4 rounded-xl border border-gray-700/50 text-xs">
                <p class="text-gray-400">Target Surat: <span id="textNoSurat" class="text-white font-semibold"></span></p>
                <p class="text-gray-400">Perihal: <span id="textPerihal" class="text-white"></span></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Nomor Agenda / Disposisi</label>
                <input type="text" id="no_dispo" name="no_dispo" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-100 focus:outline-none focus:border-indigo-500 transition-colors" placeholder="Contoh: DSP/2026/V/0987">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Instruksi KADIV / KABAG</label>
                <textarea id="disposisi_kabag" name="disposisi_kabag" required rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-100 focus:outline-none focus:border-indigo-500 transition-colors" placeholder="Masukkan instruksi utama pimpinan untuk pelaksana..."></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Instruksi Tambahan KASUBAG (Opsional)</label>
                <textarea id="disposisi_kasubag" name="disposisi_kasubag" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-100 focus:outline-none focus:border-indigo-500 transition-colors" placeholder="Catatan tambahan koordinasi lapis dua..."></textarea>
            </div>
            
            <div class="pt-2 flex justify-end space-x-3">
                <button type="button" onclick="closeDisposisiModal()" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-300 font-medium rounded-xl text-sm transition-colors">Batal</button>
                <button type="submit" id="btnSubmitDisposisi" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-medium rounded-xl text-sm transition-colors flex items-center space-x-2 shadow-lg">
                    <i class="fas fa-paper-plane text-xs"></i>
                    <span>Kirim & Disposisi</span>
                </button>
            </div>
        </form>
    </div>
</div>
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

        // 1. Jalankan fetch data tabel utama polosan (Aman, tabel dijamin keluar!)
        fetchSuratMasuk(currentPage);

        // =========================================================================
        // PERBAIKAN URUTAN: Ambil data DULU, buka modal, BARU bersihkan URL
        // =========================================================================
        let urlParams = new URLSearchParams(window.location.search);
        let autoDispoId = urlParams.get('autodispo');
        let urlNoSurat = urlParams.get('no_surat');
        let urlPerihal = urlParams.get('perihal');
        
        if (autoDispoId && currentRole === 'pimpinan') {
            // Ekstrak data selagi parameter URL masih eksis
            let finalNoSurat = urlNoSurat ? decodeURIComponent(urlNoSurat) : 'Arsip/' + autoDispoId;
            let finalPerihal = urlPerihal ? decodeURIComponent(urlPerihal) : 'Menunggu Instruksi Komando';
            
            // Buka modal secara instan
            setTimeout(() => {
                openDisposisiModal(autoDispoId, finalNoSurat, finalPerihal);
            }, 150);

            // KUNCI AMAN: Hapus parameter URL DI SINI (Setelah semua data selesai diambil)
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        // Submit Tambah Surat (Admin Only)
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
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan data arsip.', background: '#1f2937', color: '#fff' });
                },
                complete: function() {
                    $('#btnSubmitTambah').prop('disabled', false).html('<span>Simpan Arsip</span>');
                }
            });
        });

        // Submit Disposisi (Pimpinan Only)
        $('#disposisiForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#dispoSuratId').val();
            let formData = $(this).serialize();

            Swal.fire({
                title: 'Memproses Disposisi...',
                text: 'Sedang menyimpan lembar komando dan mengirim notifikasi WhatsApp Fonnte.',
                allowOutsideClick: false,
                background: '#1f2937',
                color: '#fff',
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: `{{ url('/api/surat-masuk/update') }}/${id}`,
                type: "POST",
                data: formData,
                success: function(res) {
                    Swal.fire({ icon: 'success', title: 'Sukses!', text: res.message, background: '#1f2937', color: '#fff', confirmButtonColor: '#4f46e5' });
                    closeDisposisiModal();
                    $('#disposisiForm')[0].reset();
                    fetchSuratMasuk(currentPage);
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memproses disposisi pimpinan.';
                    Swal.fire({ icon: 'error', title: 'Aksi Gagal', text: errorMsg, background: '#1f2937', color: '#fff', confirmButtonColor: '#ef4444' });
                }
            });
        });
    });

    // SCAN AI FORM (Admin Only)
    function triggerAutoScan() {
        let fileInput = document.getElementById('file_pdf_input');
        if (!fileInput || fileInput.files.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Pilih File', text: 'Silakan pilih file PDF surat terlebih dahulu sebelum melakukan scanning.', background: '#1f2937', color: '#fff' });
            return;
        }

        let formData = new FormData();
        formData.append('file_pdf', fileInput.files[0]);
        $('#btnAutoScan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Scanning...');

        $.ajax({
            url: "{{ url('/api/surat-masuk/parse') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.status === 200 && res.data) {
                    $('input[name="no_surat"]').val(res.data.no_surat || '');
                    $('input[name="tanggal_masuk"]').val(res.data.tanggal_masuk || '');
                    $('input[name="dari"]').val(res.data.dari || '');
                    $('input[name="kepada"]').val(res.data.kepada || '');
                    $('textarea[name="perihal"]').val(res.data.perihal || '');
                    Swal.fire({ icon: 'success', title: 'Scan Sukses', text: 'Data form berhasil terisi otomatis oleh AI.', background: '#1f2937', color: '#fff' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Data gagal diurai.', background: '#1f2937', color: '#fff' });
                }
            },
            error: function(xhr) {
                let errorText = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menganalisis dokumen menggunakan AI.';
                Swal.fire({ icon: 'error', title: 'Gagal Scan', text: errorText, background: '#1f2937', color: '#fff' });
            },
            complete: function() {
                $('#btnAutoScan').prop('disabled', false).html('<i class="fas fa-robot"></i> <span>Auto Scan</span>');
            }
        });
    }

    // SATU FUNGSI FETCH SURAT MASUK (Murni narik data ke tabel tanpa kepengaruh URL)
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
                $('#tableBody').html('<tr><td colspan="6" class="text-center py-6 text-red-400">Gagal memuat data surat masuk.</td></tr>');
            }
        });
    }

    function renderTable(data) {
        let html = '';
        if (!data || data.length === 0) {
            html = '<tr><td colspan="6" class="text-center py-6 text-gray-500">Tidak ada arsip surat masuk ditemukan.</td></tr>';
            $('#tableBody').html(html);
            return;
        }

        data.forEach(row => {
            let badgeColor = row.status === 'pending' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
            
            let slaWarningTag = '';
            if (row.status === 'pending' && row.tanggal_masuk) {
                let tglMasuk = new Date(row.tanggal_masuk);
                let tglSekarang = new Date();
                let selisihWaktu = tglSekarang.getTime() - tglMasuk.getTime();
                let selisihHari = Math.floor(selisihWaktu / (1000 * 3600 * 24));
                
                if (selisihHari >= 3) {
                    slaWarningTag = `<span class="ml-2 px-1.5 py-0.5 text-[9px] font-black bg-red-500 text-white rounded animate-pulse tracking-wide uppercase">Overdue SLA</span>`;
                }
            }

            let fileButton = row.file_pdf 
                ? `<a href="{{ url('/uploads') }}/${row.file_pdf}" target="_blank" class="text-indigo-400 hover:text-indigo-300 transition-colors" title="Lihat PDF"><i class="fas fa-file-pdf text-base"></i></a>` 
                : `<span class="text-gray-600">-</span>`;

            let tombolAksi = '';
            let safePerihal = row.perihal ? row.perihal.replace(/"/g, '&quot;').replace(/'/g, '&#39;') : '';

            if (currentRole === 'pimpinan') {
                if (row.status === 'pending') {
                    tombolAksi = `
                        <button onclick="openDisposisiModal(${row.id}, '${row.no_surat}', '${safePerihal}')" class="px-3 py-1.5 bg-amber-600/20 hover:bg-amber-600/30 border border-amber-500/30 text-xs rounded-lg text-amber-400 font-semibold transition-all flex items-center space-x-1">
                            <i class="fas fa-file-signature"></i>
                            <span>Beri Disposisi</span>
                        </button>
                    `;
                } else {
                    tombolAksi = `<span class="text-xs text-emerald-400 font-medium italic"><i class="fas fa-check-circle mr-1"></i>Didisposisikan</span>`;
                }
            } else if (currentRole === 'admin') {
                tombolAksi = row.status === 'pending' 
                    ? `<span class="text-xs text-amber-400 font-medium italic">Menunggu Tinjauan</span>`
                    : `<span class="text-xs text-emerald-400 font-medium italic">Selesai</span>`;
            } else {
                tombolAksi = row.status === 'disposisi'
                    ? `<span class="text-xs text-indigo-400 font-medium">Siap Dilaksanakan</span>`
                    : `<span class="text-xs text-gray-500">Belum Ada Perintah</span>`;
            }

            html += `
                <tr class="hover:bg-gray-700/20 transition-colors duration-150">
                    <td class="py-3.5 px-6 font-semibold text-white font-mono text-xs">
                        ${row.no_surat}
                        ${slaWarningTag}
                    </td>
                    <td class="py-3.5 px-6 text-gray-300 font-medium text-xs">${row.dari}</td>
                    <td class="py-3.5 px-6 text-gray-300 max-w-xs truncate text-xs" title="${safePerihal}">${row.perihal}</td>
                    <td class="py-3.5 px-6 text-gray-400 font-mono text-xs">${row.tanggal_masuk}</td>
                    <td class="py-3.5 px-6">
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold capitalize ${badgeColor}">${row.status}</span>
                    </td>
                    <td class="py-3.5 px-6 text-center flex items-center justify-center space-x-4">
                        ${fileButton}
                        ${tombolAksi}
                    </td>
                </tr>
            `;
        });
        $('#tableBody').html(html);
    }

    function openDisposisiModal(id, noSurat, perihal) {
        $('#dispoSuratId').val(id);
        $('#textNoSurat').text(noSurat);
        $('#textPerihal').text(perihal);
        
        $('#disposisiModal').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#dispoModalContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
        }, 10);
    }

    function closeDisposisiModal() {
        $('#dispoModalContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#disposisiModal').removeClass('flex').addClass('hidden');
        }, 300);
    }

    function renderPagination(meta) {
        $('#paginationInfo').text(`Halaman ${meta.page} dari ${meta.total_pages}`);
        let buttonsHtml = '';
        buttonsHtml += `<button onclick="fetchSuratMasuk(${meta.page - 1})" ${meta.page === 1 ? 'disabled' : ''} class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed text-xs rounded-lg font-medium text-white transition-all">Prev</button>`;
        buttonsHtml += `<button onclick="fetchSuratMasuk(${meta.page + 1})" ${meta.page === meta.total_pages || meta.total_pages === 0 ? 'disabled' : ''} class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed text-xs rounded-lg font-medium text-white transition-all">Next</button>`;
        $('#paginationButtons').html(buttonsHtml);
    }

    function handleFilter() { fetchSuratMasuk(1); }
    function openModal(id) { $(`#${id}`).removeClass('hidden'); }
    function closeModal(id) { $(`#${id}`).addClass('hidden'); }
</script>
@endpush