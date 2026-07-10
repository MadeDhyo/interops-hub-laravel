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
            <button onclick="openModal('modalTambah')" class="px-5 py-2.5 bg-divhub-blue hover:bg-blue-600 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg flex items-center space-x-2 text-sm">
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
    <div class="bg-divhub-card border border-divhub-border rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden transform transition-all duration-300">
        <div class="px-6 py-4 border-b border-divhub-border flex justify-between items-center bg-divhub-navy/50">
            <h3 class="text-lg font-display font-bold text-white">Input Surat Masuk Baru</h3>
            <button onclick="closeModal('modalTambah')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="formTambahSurat" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">No Surat</label>
                    <input type="text" id="no_surat" name="no_surat" required class="w-full px-4 py-2 bg-divhub-navy border border-divhub-border rounded-xl text-sm text-white focus:outline-none focus:border-divhub-gold">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Tanggal Masuk</label>
                    <input type="date" id="tanggal_masuk" name="tanggal_masuk" required class="w-full px-4 py-2 bg-divhub-navy border border-divhub-border rounded-xl text-sm text-white focus:outline-none focus:border-divhub-gold cursor-pointer">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Dari (Asal Surat)</label>
                <input type="text" id="dari" name="dari" required class="w-full px-4 py-2 bg-divhub-navy border border-divhub-border rounded-xl text-sm text-white focus:outline-none focus:border-divhub-gold">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Kepada (Tujuan)</label>
                <input type="text" id="kepada" name="kepada" required class="w-full px-4 py-2 bg-divhub-navy border border-divhub-border rounded-xl text-sm text-white focus:outline-none focus:border-divhub-gold">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Perihal</label>
                <textarea id="perihal" name="perihal" rows="3" required class="w-full px-4 py-2 bg-divhub-navy border border-divhub-border rounded-xl text-sm text-white focus:outline-none focus:border-divhub-gold"></textarea>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Berkas Dokumen (PDF)</label>
                <div class="flex gap-2 items-center">
                    <input type="file" id="file_pdf" name="file_pdf" accept="application/pdf" class="flex-1 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-divhub-navy file:text-divhub-gold hover:file:bg-divhub-gold/10 cursor-pointer">
                    <button type="button" id="btnAutoScan" class="px-5 py-2 bg-divhub-gold text-divhub-navy rounded-xl hover:bg-divhub-goldlight transition-all font-display font-bold flex items-center gap-2">
                        <i class="fas fa-microchip"></i> <span>Scan AI</span>
                    </button>
                </div>
            </div>
            <div class="pt-4 flex justify-end space-x-3 border-t border-divhub-border mt-6">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="px-4 py-2 bg-divhub-blue hover:bg-blue-600 text-white font-medium rounded-xl text-sm">Simpan Arsip</button>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- 2. MODAL INPUT DISPOSISI (KHUSUS PIMPINAN) -->
@if(auth()->user()->role === 'pimpinan')
<div id="disposisiModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-divhub-card border border-divhub-border w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="dispoModalContent">
        <div class="p-6 border-b border-divhub-border flex justify-between items-center bg-divhub-navy/50">
            <h3 class="text-lg font-display font-bold text-white flex items-center space-x-2">
                <i class="fas fa-gavel text-divhub-gold"></i>
                <span>Lembar Disposisi Digital</span>
            </h3>
            <button type="button" class="btn-close-dispo text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="disposisiForm" class="p-6 space-y-4">
            <input type="hidden" id="dispoSuratId" name="surat_id">
            
            <div class="space-y-1 bg-divhub-navy/50 p-4 rounded-xl border border-divhub-border/50 text-xs">
                <p class="text-gray-400">Target Surat: <span id="textNoSurat" class="text-white font-semibold"></span></p>
                <p class="text-gray-400">Perihal: <span id="textPerihal" class="text-white"></span></p>
            </div>
            
            <!-- NOMOR AGENDA DIHAPUS DARI SINI -->

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Instruksi KADIV / KABAG</label>
                <div class="relative">
                    <select id="disposisi_kabag" name="disposisi_kabag" required class="w-full bg-divhub-navy border border-divhub-border rounded-xl px-4 py-3 text-sm text-gray-100 focus:outline-none focus:border-divhub-gold appearance-none cursor-pointer">
                        <option value="" disabled selected>-- Pilih Instruksi Komando --</option>
                        <option value="Tindak Lanjuti (TLJ)">Tindak Lanjuti (TLJ)</option>
                        <option value="Datakan">Datakan</option>
                        <option value="Laporkan Hasilnya">Laporkan Hasilnya</option>
                        <option value="Untuk Diketahui (UDK)">Untuk Diketahui (UDK)</option>
                    </select>
                    <!-- Custom Arrow bawaan Tailwind biar selaras dengan tema Navy-Gold -->
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-divhub-gold">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Instruksi KASUBAG (Opsional)</label>
                <textarea id="disposisi_kasubag" name="disposisi_kasubag" rows="2" class="w-full bg-divhub-navy border border-divhub-border rounded-xl px-4 py-3 text-sm text-gray-100 focus:outline-none focus:border-divhub-gold" placeholder="Catatan tambahan koordinasi lapis dua..."></textarea>
            </div>
            
            <div class="pt-2 flex justify-end space-x-3">
                <button type="button" class="btn-close-dispo px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-300 font-medium rounded-xl text-sm transition-colors">Batal</button>
                <button type="submit" id="btnSubmitDisposisi" class="px-5 py-2.5 bg-divhub-gold text-divhub-navy font-display font-bold rounded-xl text-sm flex items-center space-x-2 transition-all hover:bg-divhub-goldlight hover:-translate-y-0.5 shadow-lg">
                    <i class="fas fa-paper-plane text-xs"></i> <span>Kirim Disposisi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<div id="detailDisposisiModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-divhub-card border border-divhub-border w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="detailDispoContent">
        <div class="p-6 border-b border-divhub-border flex justify-between items-center bg-divhub-navy/50">
            <h3 class="text-lg font-display font-bold text-white flex items-center space-x-2">
                <i class="fas fa-file-alt text-divhub-blue"></i>
                <span>Nota Komando</span>
            </h3>
            <button type="button" class="btn-close-detail text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 space-y-4 text-sm">
            <div class="space-y-1 bg-divhub-navy/50 p-4 rounded-xl border border-divhub-border/50 text-xs">
                <p class="text-gray-400">No. Surat: <span id="viewNoSurat" class="text-white font-semibold"></span></p>
                <p class="text-gray-400">Perihal: <span id="viewPerihal" class="text-white"></span></p>
            </div>
            
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">No Agenda</label>
                <div id="viewNoDispo" class="w-full bg-divhub-navy border border-divhub-border rounded-xl px-4 py-3 text-white font-mono"></div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Instruksi KADIV / KABAG</label>
                <div id="viewInstruksiKabag" class="w-full bg-divhub-navy border border-divhub-border rounded-xl px-4 py-3 text-white whitespace-pre-line"></div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Instruksi KASUBAG</label>
                <div id="viewInstruksiKasubag" class="w-full bg-divhub-navy border border-divhub-border rounded-xl px-4 py-3 text-white whitespace-pre-line"></div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="button" class="btn-close-detail px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm transition-colors">Tutup Dokumen</button>
            </div>
        </div>
    </div>
</div>

<div id="detailDisposisiModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-gray-800 border border-gray-700 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="detailDispoContent">
        <div class="p-6 border-b border-gray-700 flex justify-between items-center bg-gray-800/50">
            <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                <i class="fas fa-file-alt text-indigo-400"></i>
                <span>Nota Komando / Lembar Disposisi</span>
            </h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 space-y-4 text-sm">
            <div class="space-y-1 bg-gray-900/50 p-4 rounded-xl border border-gray-700/50 text-xs">
                <p class="text-gray-400">No. Surat: <span id="viewNoSurat" class="text-white font-semibold"></span></p>
                <p class="text-gray-400">Perihal: <span id="viewPerihal" class="text-white"></span></p>
            </div>
            
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Agenda / Disposisi</label>
                <div id="viewNoDispo" class="w-full bg-gray-900/40 border border-gray-700 rounded-xl px-4 py-3 text-white font-mono"></div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Instruksi KADIV / KABAG</label>
                <div id="viewInstruksiKabag" class="w-full bg-gray-900/40 border border-gray-700 rounded-xl px-4 py-3 text-white whitespace-pre-line"></div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Instruksi Tambahan KASUBAG</label>
                <div id="viewInstruksiKasubag" class="w-full bg-gray-900/40 border border-gray-700 rounded-xl px-4 py-3 text-white whitespace-pre-line"></div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="button" onclick="closeDetailModal()" class="px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm transition-colors">Tutup Dokumen</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let currentRole = "{{ auth()->user()->role }}";

    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        fetchSuratMasuk(currentPage);

        // PENANGANAN PARAMETER URL
        let urlParams = new URLSearchParams(window.location.search);
        let autoDispoId = urlParams.get('autodispo');
        let urlNoSurat = urlParams.get('no_surat');
        let urlPerihal = urlParams.get('perihal');
        
        if (autoDispoId && currentRole === 'pimpinan') {
            let finalNoSurat = urlNoSurat ? decodeURIComponent(urlNoSurat) : 'Arsip/' + autoDispoId;
            let finalPerihal = urlPerihal ? decodeURIComponent(urlPerihal) : 'Menunggu Instruksi Komando';
            
            setTimeout(() => {
                triggerDispoModal(autoDispoId, finalNoSurat, finalPerihal);
            }, 150);
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        // DELEGATED EVENT LISTENERS (KEBAL ERROR RENDER)
        // Listener Tombol "Beri Disposisi"
        $(document).on('click', '.btn-open-dispo', function() {
            let id = $(this).data('id');
            let no = decodeURIComponent($(this).data('no') || '');
            let perihal = decodeURIComponent($(this).data('perihal') || '');
            triggerDispoModal(id, no, perihal);
        });

        // Listener Tombol "Lihat Nota"
        $(document).on('click', '.btn-open-detail', function() {
            let no = decodeURIComponent($(this).data('no') || '');
            let perihal = decodeURIComponent($(this).data('perihal') || '');
            let dispo = decodeURIComponent($(this).data('dispo') || '-');
            let kabag = decodeURIComponent($(this).data('kabag') || '-');
            let kasubag = decodeURIComponent($(this).data('kasubag') || '-');
            
            $('#viewNoSurat').text(no);
            $('#viewPerihal').text(perihal);
            $('#viewNoDispo').text(dispo);
            $('#viewInstruksiKabag').text(kabag);
            $('#viewInstruksiKasubag').text(kasubag);

            $('#detailDisposisiModal').removeClass('hidden').addClass('flex');
            setTimeout(() => { $('#detailDispoContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'); }, 50);
        });

        // Listener Tombol Tutup Modal
        $(document).on('click', '.btn-close-dispo', function() {
            $('#dispoModalContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
            setTimeout(() => { $('#disposisiModal').removeClass('flex').addClass('hidden'); }, 300);
        });

        $(document).on('click', '.btn-close-detail', function() {
            $('#detailDispoContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
            setTimeout(() => { $('#detailDisposisiModal').removeClass('flex').addClass('hidden'); }, 300);
        });

        // ACTION SCAN AI (Admin)
        $('#btnAutoScan').on('click', function(e) {
            e.preventDefault();
            let fileInput = $('#file_pdf')[0].files[0];
            
            if (!fileInput) {
                Swal.fire({ icon: 'warning', title: 'File Kosong', text: 'Upload dokumen PDF terlebih dahulu.', background: '#1C2541', color: '#F3F4F6', confirmButtonColor: '#D4AF37' });
                return;
            }

            let formData = new FormData();
            formData.append('file_pdf', fileInput);

            Swal.fire({
                title: 'Menganalisis Dokumen',
                html: 'AI sedang mengekstrak data dari hasil scan. Mohon tunggu...',
                background: '#1C2541', color: '#F3F4F6', allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '/api/surat-masuk/parse',
                type: 'POST',
                data: formData,
                contentType: false, processData: false,
                success: function(response) {
                    Swal.close();
                    if(response.status === 200) {
                        // Mendapatkan tanggal lokal dalam format YYYY-MM-DD 
                        const d = new Date();
                        const offset = d.getTimezoneOffset();
                        const localDate = new Date(d.getTime() - (offset * 60 * 1000));
                        const hariIniIndo = localDate.toISOString().split('T')[0];

                        // Isi form otomatis dengan data yang diekstrak
                        $('#no_surat').val(response.data.no_surat);
                        $('#tanggal_masuk').val(hariIniIndo); // Menggunakan tanggal hari ini sebagai default
                        $('#dari').val(response.data.dari);
                        $('#kepada').val(response.data.kepada);
                        $('#perihal').val(response.data.perihal);
                        
                        Swal.fire({ icon: 'success', title: 'Scan Selesai!', text: 'Data berhasil diekstrak dengan presisi.', background: '#1C2541', color: '#F3F4F6', confirmButtonColor: '#D4AF37' });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                    Swal.fire({ icon: 'error', title: 'Gagal Scan', text: errorMsg, background: '#1C2541', color: '#F3F4F6', confirmButtonColor: '#ef4444' });
                }
            });
        });

        // FORM SUBMISSIONS
        $('#formTambahSurat').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $('#btnSubmitTambah').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "{{ url('/api/surat-masuk') }}",
                type: "POST", data: formData, contentType: false, processData: false,
                success: function(res) {
                    closeModal('modalTambah');
                    $('#formTambahSurat')[0].reset();
                    Swal.fire({ icon: 'success', title: 'Sukses', text: res.message, background: '#1C2541', color: '#F3F4F6', confirmButtonColor: '#D4AF37' });
                    fetchSuratMasuk(1);
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan arsip.', background: '#1C2541', color: '#F3F4F6', confirmButtonColor: '#ef4444' });
                },
                complete: function() {
                    $('#btnSubmitTambah').prop('disabled', false).text('Simpan Arsip');
                }
            });
        });

        $('#disposisiForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#dispoSuratId').val();
            let formData = $(this).serialize();
            $('#btnSubmitDisposisi').prop('disabled', true);

            $.ajax({
                url: `{{ url('/api/surat-masuk/update') }}/${id}`,
                type: "POST", data: formData,
                success: function(res) {
                    Swal.fire({ icon: 'success', title: 'Sukses!', text: res.message, background: '#1C2541', color: '#F3F4F6', confirmButtonColor: '#D4AF37' });
                    $('.btn-close-dispo').click();
                    $('#disposisiForm')[0].reset();
                    fetchSuratMasuk(currentPage);
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memproses.';
                    Swal.fire({ icon: 'error', title: 'Aksi Gagal', text: errorMsg, background: '#1C2541', color: '#F3F4F6', confirmButtonColor: '#ef4444' });
                },
                complete: function() {
                    $('#btnSubmitDisposisi').prop('disabled', false);
                }
            });
        });
    });

    // FUNCTIONS
    function triggerDispoModal(id, no, perihal) {
        $('#dispoSuratId').val(id);
        $('#textNoSurat').text(no);
        $('#textPerihal').text(perihal);
        
        // Hapus reset $('#no_dispo') karena elementnya sudah dibuang
        $('#disposisi_kabag').val('');
        $('#disposisi_kasubag').val('');

        $('#disposisiModal').removeClass('hidden').addClass('flex');
        setTimeout(() => { $('#dispoModalContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'); }, 50);
    }

    function fetchSuratMasuk(page) {
        currentPage = page;
        $.ajax({
            url: "{{ url('/api/surat-masuk') }}",
            type: "GET",
            data: { page: page, search: $('#searchFilter').val(), start_date: $('#startDateFilter').val(), end_date: $('#endDateFilter').val() },
            dataType: "json",
            success: function(res) {
                renderTable(res.data);
                renderPagination(res.pagination);
            }
        });
    }

    function renderTable(data) {
        let html = '';
        if (!data || data.length === 0) {
            $('#tableBody').html('<tr><td colspan="6" class="text-center py-6 text-gray-500">Tidak ada arsip.</td></tr>');
            return;
        }

        data.forEach(row => {
            let badgeColor = row.status === 'pending' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
            
            let safeNoSurat = encodeURIComponent(row.no_surat || '');
            let safePerihal = encodeURIComponent(row.perihal || '');
            let tombolAksi = '';

            if (currentRole === 'pimpinan') {
                if (row.status === 'pending') {
                    tombolAksi = `<button type="button" class="btn-open-dispo px-3 py-1.5 bg-divhub-gold/10 hover:bg-divhub-gold/20 border border-divhub-gold/40 text-xs rounded-lg text-divhub-gold font-semibold transition-all flex items-center space-x-1" data-id="${row.id}" data-no="${safeNoSurat}" data-perihal="${safePerihal}"><i class="fas fa-file-signature text-[10px]"></i><span>Beri Disposisi</span></button>`;
                } else {
                    let safeDispo = encodeURIComponent(row.no_dispo || '-');
                    let safeKabag = encodeURIComponent(row.disposisi_kabag || '-');
                    let safeKasubag = encodeURIComponent(row.disposisi_kasubag || '-');
                    tombolAksi = `<button type="button" class="btn-open-detail px-3 py-1.5 bg-divhub-blue/10 hover:bg-divhub-blue/20 border border-divhub-blue/30 text-xs rounded-lg text-blue-400 font-semibold transition-all flex items-center space-x-1" data-no="${safeNoSurat}" data-perihal="${safePerihal}" data-dispo="${safeDispo}" data-kabag="${safeKabag}" data-kasubag="${safeKasubag}"><i class="fas fa-eye text-[10px]"></i><span>Lihat Nota</span></button>`;
                }
            } else if (currentRole === 'admin') {
                tombolAksi = row.status === 'pending' ? `<span class="text-xs text-amber-400 italic">Menunggu Tinjauan</span>` : `<span class="text-xs text-emerald-400 italic">Selesai</span>`;
            } else {
                if (row.status === 'disposisi') {
                    let safeDispo = encodeURIComponent(row.no_dispo || '-');
                    let safeKabag = encodeURIComponent(row.disposisi_kabag || '-');
                    let safeKasubag = encodeURIComponent(row.disposisi_kasubag || '-');
                    tombolAksi = `<button type="button" class="btn-open-detail px-3 py-1.5 bg-indigo-600/20 border border-indigo-500/30 text-xs rounded-lg text-indigo-400 font-semibold flex items-center space-x-1" data-no="${safeNoSurat}" data-perihal="${safePerihal}" data-dispo="${safeDispo}" data-kabag="${safeKabag}" data-kasubag="${safeKasubag}"><i class="fas fa-eye"></i><span>Lihat Disposisi</span></button>`;
                } else {
                    tombolAksi = `<span class="text-xs text-gray-500 italic">Belum Ada Perintah</span>`;
                }
            }

            let fileButton = row.file_pdf ? `<a href="{{ url('/uploads') }}/${row.file_pdf}" target="_blank" class="text-divhub-gold hover:text-white transition-colors"><i class="fas fa-file-pdf text-base"></i></a>` : `<span class="text-gray-600">-</span>`;

            html += `<tr class="hover:bg-divhub-card/80 transition-colors border-b border-divhub-border/50">
                <td class="py-3.5 px-6 text-white font-mono text-xs">${row.no_surat}</td>
                <td class="py-3.5 px-6 text-gray-300 text-xs">${row.dari}</td>
                <td class="py-3.5 px-6 text-gray-300 max-w-xs truncate text-xs">${row.perihal}</td>
                <td class="py-3.5 px-6 text-gray-400 font-mono text-xs">${row.tanggal_masuk}</td>
                <td class="py-3.5 px-6"><span class="px-2.5 py-1 rounded-md text-xs font-semibold ${badgeColor}">${row.status}</span></td>
                <td class="py-3.5 px-6 text-center flex items-center justify-center space-x-4">${fileButton} ${tombolAksi}</td>
            </tr>`;
        });
        $('#tableBody').html(html);
    }

    function renderPagination(meta) {
        $('#paginationInfo').text(`Halaman ${meta.page} dari ${meta.total_pages}`);
        let html = `<button onclick="fetchSuratMasuk(${meta.page - 1})" ${meta.page === 1 ? 'disabled' : ''} class="px-3 py-1.5 bg-divhub-card disabled:opacity-40 text-xs rounded-lg text-white">Prev</button>`;
        html += `<button onclick="fetchSuratMasuk(${meta.page + 1})" ${meta.page === meta.total_pages || meta.total_pages === 0 ? 'disabled' : ''} class="px-3 py-1.5 bg-divhub-card disabled:opacity-40 text-xs rounded-lg text-white">Next</button>`;
        $('#paginationButtons').html(html);
    }

    function handleFilter() { fetchSuratMasuk(1); }
    function openModal(id) { $(`#${id}`).removeClass('hidden'); }
    function closeModal(id) { $(`#${id}`).addClass('hidden'); }
</script>
@endpush