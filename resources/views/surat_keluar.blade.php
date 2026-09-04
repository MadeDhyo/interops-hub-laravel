@extends('layout.main')

@section('title', 'Surat Keluar - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide">Data Surat Keluar</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola draf konsep, paraf Kabag, nomor TAUD, dan distribusi arsip eksternal</p>
        </div>
        
        <div class="flex items-center space-x-2">
            <button onclick="exportCsv()" class="px-4 py-2.5 glass-card hover:bg-white/[0.05] border border-ops-border text-slate-300 font-medium rounded-lg transition-all duration-200 text-sm flex items-center space-x-2">
                <i class="fas fa-file-export text-xs"></i>
                <span>Export CSV</span>
            </button>
            @can('bisa-input-surkel')
            <button onclick="openModal('modalTambahKeluar')" class="px-5 py-2.5 btn-primary rounded-lg transition-all duration-200 shadow-lg flex items-center space-x-2 text-sm">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Surat Keluar</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- Role Context Banner -->
    @include('components.role_banner')

    <!-- Subbag Filter (Urmin, Kabag, Admin) -->
    @if(auth()->user()->canSeeAllSubbag())
    <div class="glass-card px-5 py-3 rounded-xl flex items-center space-x-3 border border-ops-border/30">
        <span class="section-eyebrow">Filter Subbag:</span>
        <div class="flex flex-wrap gap-2">
            <button onclick="setSubbagFilter(null)" id="fAll" class="subbag-filter-btn px-3 py-1 rounded-md text-xs font-semibold transition-all bg-ops-cyan/20 border border-ops-cyan/40 text-ops-cyan">Semua</button>
            @foreach(['urmin','bhi','bi','ops','koor'] as $sb)
            <button onclick="setSubbagFilter('{{ $sb }}')" id="f{{ $sb }}" class="subbag-filter-btn px-3 py-1 rounded-md text-xs font-semibold transition-all border border-ops-border/40 text-slate-400 hover:border-ops-cyan/40 hover:text-ops-cyan">{{ strtoupper($sb) }}</button>
            @endforeach
        </div>
        
        @if(auth()->user()->role === 'kabag')
        <div class="ml-4 pl-4 border-l border-ops-border flex items-center">
            <button onclick="toggleUrgentFilter()" id="fUrgent" class="px-3 py-1.5 rounded-md text-xs font-bold transition-all border border-red-500/40 text-slate-400 hover:text-red-400 hover:border-red-500 flex items-center space-x-1.5">
                <i class="fas fa-exclamation-triangle"></i>
                <span>URGENT (> 3 Hari)</span>
            </button>
        </div>
        @endif
    </div>
    @endif

    <!-- Filter Bar -->
    <div class="glass-card p-5 rounded-xl grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pencarian Smart</label>
            <input type="text" id="searchFilter" placeholder="Cari nomor, peruntukan, tujuan..." class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none text-sm">
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Paraf Kabag</label>
            <select id="statusParafFilter" class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 focus:outline-none text-sm">
                <option value="">Semua Status Paraf</option>
                <option value="pending">Pending Paraf</option>
                <option value="urgent">Urgent (> 3 Hari Belum Paraf)</option>
                <option value="disetujui">Disetujui (Diparaf)</option>
                <option value="ditolak">Ditolak / Revisi</option>
            </select>
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Surat</label>
            <input type="date" id="startDateFilter" class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 focus:outline-none text-sm">
        </div>
        <div>
            <button onclick="handleFilter()" class="w-full py-2.5 glass-card hover:bg-white/[0.05] border border-ops-border text-slate-300 font-medium rounded-lg transition-all duration-200 text-sm flex justify-center items-center space-x-2">
                <i class="fas fa-filter text-xs"></i>
                <span>Terapkan Filter</span>
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-ops-border text-[10px] font-mono font-semibold uppercase tracking-widest text-slate-600 bg-ops-abyss/40">
                        <th class="py-4 px-6">No. Surat</th>
                        <th class="py-4 px-6">Ditujukan Ke</th>
                        <th class="py-4 px-6">Peruntukan / Satker</th>
                        <th class="py-4 px-6">Perihal</th>
                        @if(auth()->user()->canSeeAllSubbag())
                        <th class="py-4 px-6">Subbag</th>
                        @endif
                        <th class="py-4 px-6">Status Paraf</th>
                        <th class="py-4 px-6 text-center">Berkas</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="text-sm divide-y divide-ops-border">
                    <tr><td colspan="8"><div class="skeleton-row"><div class="skeleton-cell" style="width:15%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:25%"></div><div class="skeleton-cell" style="width:10%"></div><div class="skeleton-cell" style="width:10%"></div></div></td></tr>
                    <tr><td colspan="8"><div class="skeleton-row"><div class="skeleton-cell" style="width:18%"></div><div class="skeleton-cell" style="width:22%"></div><div class="skeleton-cell" style="width:18%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:12%"></div><div class="skeleton-cell" style="width:10%"></div></div></td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="p-5 border-t border-ops-border flex justify-between items-center">
            <p id="paginationInfo" class="text-xs text-gray-400">Menampilkan halaman 1</p>
            <div class="flex space-x-2" id="paginationButtons"></div>
        </div>
    </div>
</div>

{{-- ===== MODAL INPUT SURAT KELUAR ===== --}}
@can('bisa-input-surkel')
<div id="modalTambahKeluar" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden transform transition-all duration-300 max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-ops-border flex justify-between items-center flex-shrink-0">
            <div>
                <h3 class="text-lg font-bold text-white">Input Konsep / Surat Keluar Baru</h3>
                <p class="text-xs text-ops-cyan section-eyebrow mt-0.5">Draf konsep konseptor diajukan ke Kasubbag &amp; Kabag</p>
            </div>
            <button onclick="closeModal('modalTambahKeluar')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="formTambahSuratKeluar" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto flex-1">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">No Surat / Draft</label>
                    <input type="text" name="no_surat" required placeholder="B/ND/.../DHI (atau Draf-01)" class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Tanggal Surat</label>
                    <input type="date" name="tanggal_surat" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Dari (Pengirim/Konseptor)</label>
                    <input type="text" name="dari" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none" value="{{ Auth::user()->nama_lengkap }} ({{ strtoupper(Auth::user()->subbag ?? 'Bag') }})">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Tanggal Input</label>
                    <input type="date" name="tanggal_input" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Kepada (Instansi / Bagian Tujuan)</label>
                <input type="text" name="kepada" required placeholder="Contoh: Ses NCB Interpol / TAUD / Polda Metro Jaya" class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none">
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-ops-cyan uppercase flex items-center space-x-1">
                    <i class="fas fa-info-circle"></i>
                    <span>Catatan Peruntukan Surat (SOP Wajib)</span>
                </label>
                <textarea name="keterangan_tujuan" rows="2" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none" placeholder="Rincikan surat keluar ini buat apa, ke satker/bag mana, dan peruntukannya (misal: Follow up operasi X / Balasan nota dinas Y)..."></textarea>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Perihal</label>
                <textarea name="perihal" rows="2" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none" placeholder="Rincian perihal surat keluar..."></textarea>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Berkas Dokumen PDF (Konsep / Scan Clear)</label>
                <div id="drop-area-keluar" class="w-full relative border-2 border-dashed border-gray-600 rounded-xl p-5 flex flex-col items-center justify-center text-center cursor-pointer hover:border-ops-cyan hover:bg-white/5 transition-all group">
                    <input type="file" id="file_pdf_keluar" name="file_pdf" accept="application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 group-hover:text-ops-cyan mb-2 transition-colors"></i>
                    <p class="text-xs text-gray-300 font-medium"><span class="text-ops-cyan">Klik untuk upload</span> atau drag & drop file PDF ke sini</p>
                    <p id="file-name-keluar" class="text-xs text-ops-cyan mt-1 hidden"></p>
                </div>
            </div>

            <div class="pt-4 border-t border-ops-border flex justify-end space-x-3">
                <button type="button" onclick="closeModal('modalTambahKeluar')" class="px-4 py-2 glass-card hover:bg-white/[0.05] border border-ops-border text-slate-300 rounded-lg text-sm">Batal</button>
                <button type="submit" id="btnSubmitKeluar" class="px-5 py-2 btn-primary rounded-lg text-sm font-bold flex items-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan Surat Keluar</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ===== MODAL PARAF / PERSETUJUAN KABAG ===== --}}
@can('bisa-paraf-kabag')
<div id="modalParafKabag" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg shadow-2xl border border-ops-border overflow-hidden">
        <div class="px-6 py-4 border-b border-ops-border flex justify-between items-center bg-ops-deep/60">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-ops-violet/15 text-ops-violet">
                    <i class="fas fa-stamp text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Persetujuan &amp; Paraf Kabag</h3>
                    <p class="section-eyebrow text-slate-400 mt-0.5">TTD DIGITAL HARIAN 1-KLIK (REUSABLE SIGNATURE)</p>
                </div>
            </div>
            <button onclick="closeModal('modalParafKabag')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>

        <form id="formParafKabag" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="paraf_surat_id" name="surat_id">
            
            <div class="p-3.5 rounded-xl bg-ops-abyss/80 border border-ops-border/60 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">No. Surat:</span>
                    <span id="paraf_no_surat" class="font-mono text-white font-bold">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Kepada:</span>
                    <span id="paraf_kepada" class="text-slate-200">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Subbag Pembuat:</span>
                    <span id="paraf_subbag" class="font-bold text-ops-cyan uppercase">-</span>
                </div>
                <div class="pt-1 border-t border-ops-border/40">
                    <span class="text-slate-400 block mb-0.5">Peruntukan:</span>
                    <p id="paraf_peruntukan" class="text-slate-300 italic text-[11px]">-</p>
                </div>
            </div>

            <div id="dailySigStatusBox" class="p-3.5 rounded-xl border transition-all text-xs">
                <!-- Injected via JS based on today's signature check -->
            </div>

            <div id="sigUploadContainer" class="space-y-1.5 hidden">
                <label class="text-xs font-semibold text-gray-300 uppercase">Gambar TTD untuk Hari Ini (Drawbox)</label>
                <div class="border border-ops-border rounded-lg overflow-hidden bg-white/5 relative">
                    <canvas id="signatureCanvas" class="w-full h-40 cursor-crosshair touch-none bg-white"></canvas>
                    <div class="absolute bottom-2 right-2 flex gap-2">
                        <button type="button" id="btnClearSignature" class="px-3 py-1 bg-gray-700/80 hover:bg-red-500/80 text-white rounded text-[10px] uppercase font-bold tracking-wider transition-colors">Hapus</button>
                    </div>
                </div>
                <input type="hidden" id="signature_base64" name="signature_base64">
                <p class="text-[10px] text-slate-400">Silakan gambar tanda tangan Anda di dalam kotak di atas.</p>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Catatan / Arahan Pimpinan (Opsional)</label>
                <textarea id="paraf_catatan" name="catatan" rows="2" class="w-full px-4 py-2 ops-input rounded-lg text-xs text-white focus:outline-none" placeholder="Catatan koreksi atau instruksi distribusi khusus..."></textarea>
            </div>

            <div class="pt-4 border-t border-ops-border flex justify-between items-center">
                <button type="button" onclick="submitParafDecision('ditolak')" class="px-4 py-2 rounded-lg bg-red-500/15 hover:bg-red-500/25 border border-red-500/40 text-red-400 text-xs font-bold transition-all flex items-center space-x-1.5">
                    <i class="fas fa-times-circle"></i>
                    <span>Tolak / Revisi</span>
                </button>
                <div class="flex space-x-2">
                    <button type="button" onclick="closeModal('modalParafKabag')" class="px-4 py-2 glass-card border border-ops-border text-slate-300 rounded-lg text-xs">Batal</button>
                    <button type="button" onclick="submitParafDecision('disetujui')" id="btnApproveParaf" class="px-5 py-2 btn-primary rounded-lg text-xs font-bold flex items-center space-x-1.5">
                        <i class="fas fa-check-double"></i>
                        <span>Setujui &amp; Paraf</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ===== MODAL LIHAT HASIL PARAF KABAG ===== --}}
<div id="modalLihatParaf" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg shadow-2xl border border-ops-border overflow-hidden">
        <div class="px-6 py-4 border-b border-ops-border flex justify-between items-center bg-ops-deep/60">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-ops-cyan/15 text-ops-cyan">
                    <i class="fas fa-eye text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Detail Keputusan Paraf Kabag</h3>
                    <p class="section-eyebrow text-slate-400 mt-0.5">HASIL KEPUTUSAN PIMPINAN</p>
                </div>
            </div>
            <button onclick="closeModal('modalLihatParaf')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6 space-y-4">
            <div class="p-3.5 rounded-xl bg-ops-abyss/80 border border-ops-border/60 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">No. Surat:</span>
                    <span id="view_paraf_no_surat" class="font-mono text-white font-bold">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Keputusan:</span>
                    <span id="view_paraf_status" class="font-bold text-white uppercase">-</span>
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Catatan / Arahan Kabag</label>
                <div id="view_paraf_catatan" class="w-full px-4 py-3 bg-white/5 border border-ops-border/40 rounded-lg text-xs text-white min-h-[60px] whitespace-pre-line"></div>
            </div>
            <div class="pt-4 border-t border-ops-border flex justify-end">
                <button type="button" onclick="closeModal('modalLihatParaf')" class="px-5 py-2 glass-card border border-ops-border text-slate-300 rounded-lg text-xs font-bold transition-all hover:bg-white/10">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let currentSubbagFilter = null;
    const canSeeAllSubbag = {{ auth()->user()->canSeeAllSubbag() ? 'true' : 'false' }};
    const canParafKabag = {{ auth()->user()->canParaf() ? 'true' : 'false' }};

    $(document).ready(function() {
        loadData(currentPage);
        setupDragDrop();
    });

    function setupDragDrop() {
        const dropArea = document.getElementById('drop-area-keluar');
        const fileInput = document.getElementById('file_pdf_keluar');
        const fileNameDisplay = document.getElementById('file-name-keluar');

        if (!dropArea || !fileInput) return;

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('border-ops-cyan', 'bg-white/5'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('border-ops-cyan', 'bg-white/5'), false);
        });

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                fileInput.files = files;
                updateFileName(files[0].name);
            }
        }

        fileInput.addEventListener('change', function() {
            if (this.files.length) {
                updateFileName(this.files[0].name);
            }
        });

        function updateFileName(name) {
            fileNameDisplay.textContent = 'File terpilih: ' + name;
            fileNameDisplay.classList.remove('hidden');
        }
    }

    function setSubbagFilter(subbag) {
        currentSubbagFilter = subbag;
        // Update button styles
        $('.subbag-filter-btn').removeClass('bg-ops-cyan/20 border-ops-cyan/40 text-ops-cyan').addClass('border-ops-border/40 text-slate-400');
        let btnId = subbag ? '#f' + subbag : '#fAll';
        $(btnId).addClass('bg-ops-cyan/20 border-ops-cyan/40 text-ops-cyan').removeClass('border-ops-border/40 text-slate-400');
        loadData(1);
    }

    function toggleUrgentFilter() {
        if ($('#statusParafFilter').val() === 'urgent') {
            $('#statusParafFilter').val('');
            $('#fUrgent').removeClass('bg-red-500/20 text-red-400 border-red-500/80').addClass('border-red-500/40 text-slate-400');
        } else {
            $('#statusParafFilter').val('urgent');
            $('#fUrgent').addClass('bg-red-500/20 text-red-400 border-red-500/80').removeClass('border-red-500/40 text-slate-400');
        }
        loadData(1);
    }

    function handleFilter() {
        loadData(1);
    }

    function loadData(page = 1) {
        currentPage = page;
        const search = $('#searchFilter').val();
        const statusParaf = $('#statusParafFilter').val();
        const startDate = $('#startDateFilter').val();

        let url = `{{ url('/api/surat-keluar') }}?page=${page}&limit=10`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (statusParaf) url += `&status_paraf=${encodeURIComponent(statusParaf)}`;
        if (startDate) url += `&start_date=${startDate}`;
        if (currentSubbagFilter) url += `&subbag=${encodeURIComponent(currentSubbagFilter)}`;

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status === 200) {
                    renderTable(res.data);
                    renderPagination(res.pagination);
                }
            }
        });
    }

    function renderTable(data) {
        let html = '';
        const colspan = canSeeAllSubbag ? 8 : 7;

        if (!data || data.length === 0) {
            html = `<tr><td colspan="${colspan}" class="text-center py-8 text-gray-500 font-mono text-xs">Belum ada surat keluar yang terdaftar.</td></tr>`;
            $('#tableBody').html(html);
            return;
        }

        data.forEach(item => {
            let fileBtn = '<span class="text-xs text-gray-500 italic">Tidak ada</span>';
            if (item.file_pdf) {
                let fileUrl = `{{ url('/arsip/dokumen') }}/${item.file_pdf}`;
                fileBtn = `
                    <button onclick="openPdfModal('${fileUrl}', '${item.no_surat}')" class="p-2 rounded-lg bg-ops-cyan/10 hover:bg-ops-cyan/20 border border-ops-cyan/30 text-ops-cyan transition-colors" title="Lihat Dokumen">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                `;
            }

            let statusParafBadge = '<span class="stamp stamp-pending text-[9px]">PENDING</span>';
            if (item.status_paraf_kabag === 'disetujui') {
                statusParafBadge = '<span class="stamp stamp-disposisi text-[9px]">DIPARAF KABAG</span>';
            } else if (item.status_paraf_kabag === 'ditolak') {
                statusParafBadge = '<span class="stamp stamp-red text-[9px]">REVISI</span>';
            }

            let subbagCol = canSeeAllSubbag ? `<td class="py-4 px-6 font-mono text-xs"><span class="px-2 py-0.5 rounded bg-ops-cyan/10 text-ops-cyan uppercase">${item.subbag || '-'}</span></td>` : '';

            let actionBtn = '-';
            if (canParafKabag) {
                if (item.status_paraf_kabag === 'pending') {
                    actionBtn = `
                        <button onclick="openParafModal(${item.id}, '${escapeHtml(item.no_surat)}', '${escapeHtml(item.kepada)}', '${escapeHtml(item.subbag || '-')}', '${escapeHtml(item.keterangan_tujuan || '-')}')" class="px-3 py-1.5 rounded-md bg-ops-violet/15 hover:bg-ops-violet/25 border border-ops-violet/40 text-ops-violet text-xs font-bold transition-all inline-flex items-center space-x-1">
                            <i class="fas fa-file-signature"></i>
                            <span>Paraf</span>
                        </button>
                    `;
                } else {
                    actionBtn = `
                        <button onclick="openParafModal(${item.id}, '${escapeHtml(item.no_surat)}', '${escapeHtml(item.kepada)}', '${escapeHtml(item.subbag || '-')}', '${escapeHtml(item.keterangan_tujuan || '-')}')" class="px-3 py-1.5 rounded-md bg-slate-500/15 hover:bg-slate-500/25 border border-slate-500/40 text-slate-300 text-xs font-bold transition-all inline-flex items-center space-x-1">
                            <i class="fas fa-edit"></i>
                            <span>Edit Paraf</span>
                        </button>
                    `;
                }
            } else if (item.status_paraf_kabag !== 'pending') {
                actionBtn = `
                    <button onclick="openViewParafModal('${escapeHtml(item.no_surat)}', '${item.status_paraf_kabag}', '${escapeHtml(item.catatan_kabag || '-')}')" class="px-3 py-1.5 rounded-md bg-ops-cyan/15 hover:bg-ops-cyan/25 border border-ops-cyan/40 text-ops-cyan text-xs font-bold transition-all inline-flex items-center space-x-1">
                        <i class="fas fa-eye"></i>
                        <span>Lihat Paraf</span>
                    </button>
                `;
            }

            html += `
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="py-4 px-6 font-mono font-semibold text-white text-xs">${item.no_surat}</td>
                    <td class="py-4 px-6 font-medium text-slate-200 text-xs">${item.kepada}</td>
                    <td class="py-4 px-6 text-slate-300 text-xs max-w-xs truncate" title="${escapeHtml(item.keterangan_tujuan || '-')}">${item.keterangan_tujuan || '<span class="text-slate-500 italic">-</span>'}</td>
                    <td class="py-4 px-6 text-slate-300 text-xs max-w-xs truncate" title="${escapeHtml(item.perihal)}">${item.perihal}</td>
                    ${subbagCol}
                    <td class="py-4 px-6">${statusParafBadge}</td>
                    <td class="py-4 px-6 text-center">${fileBtn}</td>
                    <td class="py-4 px-6 text-center">${actionBtn}</td>
                </tr>
            `;
        });

        $('#tableBody').html(html);
    }

    function renderPagination(pagination) {
        $('#paginationInfo').text(`Halaman ${pagination.page} dari ${pagination.total_pages || 1}`);
        let buttons = '';

        if (pagination.page > 1) {
            buttons += `<button onclick="loadData(${pagination.page - 1})" class="px-3 py-1.5 glass-card hover:bg-white/[0.05] border border-ops-border text-slate-300 rounded text-xs"><i class="fas fa-chevron-left"></i></button>`;
        }
        if (pagination.page < pagination.total_pages) {
            buttons += `<button onclick="loadData(${pagination.page + 1})" class="px-3 py-1.5 glass-card hover:bg-white/[0.05] border border-ops-border text-slate-300 rounded text-xs"><i class="fas fa-chevron-right"></i></button>`;
        }

        $('#paginationButtons').html(buttons);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function exportCsv() {
        const search = $('#searchFilter').val();
        let url = `{{ url('/api/surat-keluar/export') }}?subbag=${currentSubbagFilter || ''}&search=${encodeURIComponent(search || '')}`;
        window.location.href = url;
    }

    // Submit Form Tambah Surat Keluar
    $('#formTambahSuratKeluar').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnSubmitKeluar');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ url('/api/surat-keluar') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Surat Keluar');
                if (res.status === 201) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Surat keluar berhasil disimpan & terdaftar untuk paraf Kabag!',
                        background: '#0b1628',
                        color: '#fff',
                        confirmButtonColor: '#00c6ff'
                    });
                    closeModal('modalTambahKeluar');
                    $('#formTambahSuratKeluar')[0].reset();
                    $('#file-name-keluar').addClass('hidden');
                    loadData(1);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Surat Keluar');
                let err = xhr.responseJSON?.message || 'Gagal menyimpan surat keluar.';
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: err, background: '#0b1628', color: '#fff' });
            }
        });
    });

    // Open Paraf Modal for Kabag
    let hasDailySignature = false;
    function openParafModal(id, noSurat, kepada, subbag, peruntukan) {
        $('#paraf_surat_id').val(id);
        $('#paraf_no_surat').text(noSurat);
        $('#paraf_kepada').text(kepada);
        $('#paraf_subbag').text(subbag);
        $('#paraf_peruntukan').text(peruntukan);
        $('#paraf_catatan').val('');

        // Cek status daily signature
        $.ajax({
            url: "{{ url('/api/surat-keluar/today-signature') }}",
            type: "GET",
            dataType: "json",
            success: function(res) {
                hasDailySignature = res.has_signature;
                const box = $('#dailySigStatusBox');
                const uploadContainer = $('#sigUploadContainer');

                if (res.has_signature) {
                    box.attr('class', 'p-3.5 rounded-xl border bg-emerald-500/10 border-emerald-500/30 text-emerald-300');
                    box.html(`
                        <div class="flex items-center space-x-2 font-bold">
                            <i class="fas fa-check-circle text-emerald-400"></i>
                            <span>TTD Digital Hari Ini Aktif (${res.date})</span>
                        </div>
                        <p class="text-[11px] text-slate-300 mt-1">Anda sudah memiliki tanda tangan aktif untuk hari ini. Klik 'Setujui & Paraf' untuk langsung mengaplikasikan tanda tangan secara instan.</p>
                    `);
                    uploadContainer.addClass('hidden');
                } else {
                    box.attr('class', 'p-3.5 rounded-xl border bg-ops-gold/10 border-ops-gold/30 text-ops-gold');
                    box.html(`
                        <div class="flex items-center space-x-2 font-bold">
                            <i class="fas fa-exclamation-circle text-ops-gold"></i>
                            <span>TTD Hari Ini Belum Diunggah</span>
                        </div>
                        <p class="text-[11px] text-slate-300 mt-1">Silakan upload gambar spesimen TTD Anda 1 kali untuk hari ini di bawah. Surat-surat berikutnya hari ini akan otomatis memakai TTD ini tanpa perlu upload ulang.</p>
                    `);
                    uploadContainer.removeClass('hidden');
                }
                openModal('modalParafKabag');
            }
        });
    }

    function openViewParafModal(noSurat, status, catatan) {
        $('#view_paraf_no_surat').text(noSurat);
        $('#view_paraf_status').text(status === 'disetujui' ? 'Disetujui / Diparaf' : 'Ditolak / Revisi');
        $('#view_paraf_status').removeClass('text-emerald-400 text-red-400').addClass(status === 'disetujui' ? 'text-emerald-400' : 'text-red-400');
        $('#view_paraf_catatan').text(catatan || 'Tidak ada catatan.');
        openModal('modalLihatParaf');
    }

    function submitParafDecision(status) {
        const id = $('#paraf_surat_id').val();
        const catatan = $('#paraf_catatan').val();
        const formData = new FormData();

        formData.append('status', status);
        formData.append('catatan', catatan);

        const sigBase64 = $('#signature_base64').val();
        if (sigBase64) {
            formData.append('signature_base64', sigBase64);
        }

        $.ajax({
            url: `{{ url('/api/surat-keluar') }}/${id}/paraf`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                if (res.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        background: '#0b1628',
                        color: '#fff',
                        confirmButtonColor: '#00c6ff'
                    });
                    closeModal('modalParafKabag');
                    loadData(currentPage);
                }
            },
            error: function(xhr) {
                let err = xhr.responseJSON?.message || 'Gagal memproses paraf.';
                Swal.fire({ icon: 'error', title: 'Perhatian', text: err, background: '#0b1628', color: '#fff' });
            }
        });
    }

    function openModal(modalId) {
        $('#' + modalId).removeClass('hidden');
    }

    function closeModal(modalId) {
        $('#' + modalId).addClass('hidden');
    }

    // --- CANVAS SIGNATURE PAD LOGIC ---
    const canvas = document.getElementById('signatureCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let hasDrawing = false;
        
        function resizeCanvas() {
            canvas.width = canvas.parentElement.offsetWidth;
            canvas.height = canvas.parentElement.offsetHeight;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#000000'; // Draw in black on white
        }
        
        window.addEventListener('resize', resizeCanvas);
        // Call it once when modal might show, maybe add timeout or call when modal opens
        setTimeout(resizeCanvas, 500);

        function startPosition(e) {
            isDrawing = true;
            draw(e);
        }

        function endPosition() {
            isDrawing = false;
            ctx.beginPath();
            if (hasDrawing) {
                $('#signature_base64').val(canvas.toDataURL('image/png'));
            }
        }

        function draw(e) {
            if (!isDrawing) return;
            hasDrawing = true;
            let rect = canvas.getBoundingClientRect();
            let x, y;
            if (e.touches && e.touches.length > 0) {
                x = e.touches[0].clientX - rect.left;
                y = e.touches[0].clientY - rect.top;
            } else {
                x = e.clientX - rect.left;
                y = e.clientY - rect.top;
            }
            
            ctx.lineTo(x, y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(x, y);
        }

        canvas.addEventListener('mousedown', startPosition);
        canvas.addEventListener('mouseup', endPosition);
        canvas.addEventListener('mousemove', draw);

        canvas.addEventListener('touchstart', (e) => { e.preventDefault(); startPosition(e); });
        canvas.addEventListener('touchend', endPosition);
        canvas.addEventListener('touchmove', (e) => { e.preventDefault(); draw(e); });

        $('#btnClearSignature').on('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            $('#signature_base64').val('');
            hasDrawing = false;
        });
        
        // Listen to modal open to resize properly
        const originalOpenModal = window.openModal;
        window.openModal = function(id) {
            originalOpenModal(id);
            if (id === 'modalParafKabag') {
                setTimeout(resizeCanvas, 100);
            }
        }
    }
</script>
@endpush