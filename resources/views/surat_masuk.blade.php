@extends('layout.main')

@section('title', 'Surat Masuk - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide">Data Surat Masuk</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola dokumen surat masuk, tracking status, dan distribusi disposisi</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="exportCsv()" class="px-4 py-2.5 glass-card hover:bg-white/[0.05] border border-ops-border text-slate-300 font-medium rounded-lg transition-all duration-200 text-sm flex items-center space-x-2">
                <i class="fas fa-file-export text-xs"></i>
                <span>Export CSV</span>
            </button>
            @can('bisa-input-surma')
            <button onclick="openModal('modalTambah')" class="px-5 py-2.5 btn-primary rounded-lg transition-all duration-200 shadow-lg flex items-center space-x-2 text-sm">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Surat Masuk</span>
            </button>
            @endcan
        </div>
    </div>

    <!-- Role Context Banner -->
    @include('components.role_banner')

    {{-- Filter Subbag (urmin, kabag, admin) --}}
    @if(auth()->user()->canSeeAllSubbag())
    <div class="glass-card px-5 py-3 rounded-xl flex items-center space-x-3 border border-ops-border/30">
        <span class="section-eyebrow">Filter Subbag:</span>
        <div class="flex flex-wrap gap-2">
            <button onclick="setSubbagFilter(null)" id="fAll" class="subbag-filter-btn px-3 py-1 rounded-md text-xs font-semibold transition-all bg-ops-cyan/20 border border-ops-cyan/40 text-ops-cyan">Semua</button>
            @foreach(['urmin','bhi','bi','ops','koor'] as $sb)
            <button onclick="setSubbagFilter('{{ $sb }}')" id="f{{ $sb }}" class="subbag-filter-btn px-3 py-1 rounded-md text-xs font-semibold transition-all border border-ops-border/40 text-slate-400 hover:border-ops-cyan/40 hover:text-ops-cyan">{{ strtoupper($sb) }}</button>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filter PIC (Pills Cepat untuk Subbag) --}}
    @php
        $currSb = auth()->user()->subbag;
        $picCounts = ['ops' => 3, 'koor' => 4, 'bhi' => 4, 'bi' => 3];
    @endphp
    @if($currSb && isset($picCounts[$currSb]))
    <div class="glass-card px-5 py-3 rounded-xl flex items-center space-x-3 border border-ops-border/30">
        <span class="section-eyebrow text-ops-gold flex items-center gap-1.5"><i class="fas fa-user-tag text-xs"></i> Filter PIC:</span>
        <div class="flex flex-wrap gap-2">
            <button onclick="setPicFilter(null)" id="picAll" class="pic-filter-btn px-3 py-1 rounded-md text-xs font-semibold transition-all bg-ops-gold/20 border border-ops-gold/40 text-ops-gold">Semua PIC</button>
            @for($i = 1; $i <= $picCounts[$currSb]; $i++)
            @php $picName = "Anggota " . strtoupper($currSb) . " " . $i; @endphp
            <button onclick="setPicFilter('{{ $picName }}')" class="pic-filter-btn px-3 py-1 rounded-md text-xs font-semibold transition-all border border-ops-border/40 text-slate-400 hover:border-ops-gold/40 hover:text-ops-gold">{{ $picName }}</button>
            @endfor
        </div>
    </div>
    @endif

    <div class="glass-card p-5 rounded-xl grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pencarian Smart</label>
            <input type="text" id="searchFilter" placeholder="Cari nomor, asal, perihal..." class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 placeholder-gray-500 focus:outline-none text-sm">
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Filter PIC</label>
            <div class="relative">
                <select id="picFilter" class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 focus:outline-none text-sm cursor-pointer appearance-none">
                    <option value="">Semua PIC</option>
                    @php
                        $subbagsToList = auth()->user()->canSeeAllSubbag() ? ['ops', 'koor', 'bhi', 'bi'] : ($currSb && isset($picCounts[$currSb]) ? [$currSb] : []);
                    @endphp
                    @foreach($subbagsToList as $sbKey)
                        @if(auth()->user()->canSeeAllSubbag())
                            <optgroup label="SUBBAG {{ strtoupper($sbKey) }}" class="bg-ops-abyss text-slate-300">
                        @endif
                        @for($i = 1; $i <= ($picCounts[$sbKey] ?? 0); $i++)
                            @php $pName = "Anggota " . strtoupper($sbKey) . " " . $i; @endphp
                            <option value="{{ $pName }}" class="bg-ops-abyss text-white">{{ $pName }}</option>
                        @endfor
                        @if(auth()->user()->canSeeAllSubbag())
                            </optgroup>
                        @endif
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-ops-gold">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Mulai</label>
            <input type="date" id="startDateFilter" class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 focus:outline-none text-sm">
        </div>
        <div class="space-y-1">
            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Selesai</label>
            <input type="date" id="endDateFilter" class="w-full px-4 py-2.5 ops-input rounded-lg text-gray-100 focus:outline-none text-sm">
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
                        <th class="py-4 px-6">Asal Surat</th>
                        <th class="py-4 px-6">Perihal</th>
                        <th class="py-4 px-6">Tanggal Masuk</th>
                        @if(auth()->user()->canSeeAllSubbag())
                        <th class="py-4 px-6">Subbag</th>
                        @endif
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="text-sm divide-y divide-ops-border">
                    <tr><td colspan="7"><div class="skeleton-row"><div class="skeleton-cell" style="width:15%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:25%"></div><div class="skeleton-cell" style="width:15%"></div><div class="skeleton-cell" style="width:12%"></div><div class="skeleton-cell" style="width:13%"></div></div></td></tr>
                    <tr><td colspan="7"><div class="skeleton-row"><div class="skeleton-cell" style="width:18%"></div><div class="skeleton-cell" style="width:22%"></div><div class="skeleton-cell" style="width:20%"></div><div class="skeleton-cell" style="width:14%"></div><div class="skeleton-cell" style="width:10%"></div><div class="skeleton-cell" style="width:16%"></div></div></td></tr>
                    <tr><td colspan="7"><div class="skeleton-row"><div class="skeleton-cell" style="width:12%"></div><div class="skeleton-cell" style="width:25%"></div><div class="skeleton-cell" style="width:22%"></div><div class="skeleton-cell" style="width:16%"></div><div class="skeleton-cell" style="width:11%"></div><div class="skeleton-cell" style="width:14%"></div></div></td></tr>
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-ops-border flex justify-between items-center">
            <p id="paginationInfo" class="text-xs text-gray-400">Menampilkan halaman 1</p>
            <div class="flex space-x-2" id="paginationButtons"></div>
        </div>
    </div>
</div>

{{-- ===== MODAL INPUT SURAT MASUK (Anggota Urmin & Admin) ===== --}}
@can('bisa-input-surma')
<div id="modalTambah" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-card rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden transform transition-all duration-300">
        <div class="px-6 py-4 border-b border-ops-border flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-white">Input Surat Masuk Baru</h3>
                <p class="text-xs text-ops-cyan section-eyebrow mt-0.5">Sebagai URMIN — pilih 1 atau 2 subbag tujuan</p>
            </div>
            <button onclick="closeModal('modalTambah')" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="formTambahSurat" enctype="multipart/form-data" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">No Surat</label>
                    <input type="text" id="no_surat" name="no_surat" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-400 uppercase">Tanggal Masuk</label>
                    <input type="date" id="tanggal_masuk" name="tanggal_masuk" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none cursor-pointer" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Dari (Asal Surat)</label>
                <input type="text" id="dari" name="dari" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Kepada (Tujuan)</label>
                <input type="text" id="kepada" name="kepada" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Perihal</label>
                <textarea id="perihal" name="perihal" rows="3" required class="w-full px-4 py-2 ops-input rounded-lg text-sm text-white focus:outline-none"></textarea>
            </div>

            {{-- SUBBAG TUJUAN (1-2 subbag) --}}
            <div class="space-y-2">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Subbag Tujuan <span class="text-ops-cyan">(pilih 1 atau 2)</span></label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['urmin','bhi','bi','ops','koor'] as $sb)
                    <label class="flex items-center space-x-2 glass px-3 py-2 rounded-lg cursor-pointer hover:border-ops-cyan/40 transition-all border border-transparent" id="lbl_{{ $sb }}">
                        <input type="checkbox" name="subbag_tujuan[]" value="{{ $sb }}" class="subbag-checkbox accent-cyan-400" onchange="validateSubbagCheckboxes(this)">
                        <span class="text-xs font-semibold text-slate-300 uppercase">{{ $sb }}</span>
                    </label>
                    @endforeach
                </div>
                <p class="text-[10px] text-amber-400 hidden" id="subbagWarning"><i class="fas fa-exclamation-triangle mr-1"></i>Maksimal 2 subbag tujuan</p>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase">Berkas Dokumen (PDF)</label>
                <div id="drop-area" class="w-full relative border-2 border-dashed border-gray-600 rounded-xl p-6 flex flex-col items-center justify-center text-center cursor-pointer hover:border-ops-gold hover:bg-white/5 transition-all group">
                    <input type="file" id="file_pdf" name="file_pdf" accept="application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required>
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 group-hover:text-ops-gold mb-3 transition-colors"></i>
                    <p class="text-sm text-gray-300 font-medium"><span class="text-ops-gold">Klik untuk upload</span> atau drag &amp; drop</p>
                    <p class="text-xs text-gray-500 mt-1">Hanya file PDF (Maks. 10MB)</p>
                    <div id="file-info" class="hidden mt-3 p-2 bg-ops-abyss/80 rounded-lg border border-ops-border flex items-center gap-3 w-full">
                        <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                        <div class="text-left flex-1 overflow-hidden">
                            <p id="file-name" class="text-sm font-semibold text-white truncate"></p>
                            <p id="file-size" class="text-xs text-gray-400"></p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button" id="btnAutoScan" class="px-5 py-2 btn-primary text-ops-abyss rounded-xl hover:bg-divhub-teal transition-all font-bold flex items-center gap-2">
                        <i class="fas fa-microchip"></i> <span>Scan AI (Otomatis Isi Form)</span>
                    </button>
                </div>
            </div>
            <div class="pt-4 flex justify-end space-x-3 border-t border-ops-border mt-6">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="px-4 py-2 btn-primary text-ops-abyss font-bold rounded-xl text-sm">Simpan Arsip</button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ===== MODAL DISPOSISI (Kasubbag & Kabag & Admin) ===== --}}
@can('bisa-disposisi')
<div id="disposisiModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="glass-card w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="dispoModalContent">
        <div class="p-6 border-b border-ops-border flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                <i class="fas fa-gavel text-ops-gold"></i>
                <span>Lembar Disposisi Digital</span>
            </h3>
            <button type="button" class="btn-close-dispo text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="disposisiForm" class="p-6 space-y-4">
            <input type="hidden" id="dispoSuratId" name="surat_id">
            <div class="space-y-1 bg-ops-abyss/40 p-4 rounded-xl border border-ops-border/50 text-xs">
                <p class="text-gray-400">Target Surat: <span id="textNoSurat" class="text-white font-semibold"></span></p>
                <p class="text-gray-400">Perihal: <span id="textPerihal" class="text-white"></span></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Instruksi Arahan Disposisi</label>
                <div class="relative">
                    <select id="disposisi_kabag" name="disposisi_kabag" required class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none appearance-none cursor-pointer">
                        <option value="" disabled selected>-- Pilih Arahan Disposisi --</option>
                        <option value="Tindak Lanjuti (TLJ) - Siapkan Konsep Balasan">Tindak Lanjuti (TLJ) - Siapkan Konsep Balasan</option>
                        <option value="Tunjuk PIC & Buat Draf Surat Keluar">Tunjuk PIC &amp; Buat Draf Surat Keluar</option>
                        <option value="Hadiri / Laksanakan Kegiatan">Hadiri / Laksanakan Kegiatan</option>
                        <option value="Koordinasikan dengan Bagian / Instansi Terkait">Koordinasikan dengan Bagian / Instansi Terkait</option>
                        <option value="Simpan / Diarsipkan Saja (Info Rutin Tanpa Balasan)">Simpan / Diarsipkan Saja (Info Rutin Tanpa Balasan)</option>
                        <option value="Untuk Diketahui (UDK)">Untuk Diketahui (UDK)</option>
                        <option value="Laporkan Hasilnya">Laporkan Hasilnya</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-ops-gold">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>
            @if(auth()->user()->isKasubbag() && !auth()->user()->isUrmin())
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Penunjukan PIC Anggota</label>
                <div class="relative">
                    <select id="disposisi_pic" name="disposisi_pic" class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none appearance-none cursor-pointer">
                        <option value="" selected>-- Pilih Anggota PIC (Opsional) --</option>
                        @php
                            $sb = auth()->user()->subbag;
                            $count = 0;
                            if ($sb == 'ops') $count = 3;
                            elseif ($sb == 'koor') $count = 4;
                            elseif ($sb == 'bhi') $count = 4;
                            elseif ($sb == 'bi') $count = 3;
                        @endphp
                        @for($i = 1; $i <= $count; $i++)
                            <option value="Anggota {{ strtoupper($sb) }} {{ $i }}">Anggota {{ strtoupper($sb) }} {{ $i }}</option>
                        @endfor
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-ops-gold">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>
            @endif
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Catatan Tambahan Disposisi</label>
                <textarea id="disposisi_kasubag" name="disposisi_kasubag" rows="2" class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none" placeholder="Catatan khusus disposisi..."></textarea>
            </div>
            <div class="pt-2 flex justify-end space-x-3">
                <button type="button" class="btn-close-dispo px-5 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white border border-ops-border transition-colors">Batal</button>
                <button type="submit" id="btnSubmitDisposisi" class="px-5 py-2.5 bg-ops-gold text-ops-abyss font-bold rounded-lg text-sm flex items-center space-x-2 transition-all hover:opacity-90 shadow-lg">
                    <i class="fas fa-paper-plane text-xs"></i> <span>Kirim Disposisi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

{{-- ===== MODAL LIHAT NOTA DISPOSISI (Semua yang punya akses lihat) ===== --}}
<div id="detailDisposisiModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="glass-card w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="detailDispoContent">
        <div class="p-6 border-b border-ops-border flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                <i class="fas fa-file-alt text-ops-cyan"></i>
                <span>Nota Komando</span>
            </h3>
            <button type="button" class="btn-close-detail text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 space-y-4 text-sm">
            <div class="space-y-1 bg-ops-abyss/40 p-4 rounded-xl border border-ops-border/50 text-xs">
                <p class="text-gray-400">No. Surat: <span id="viewNoSurat" class="text-white font-semibold font-mono"></span></p>
                <p class="text-gray-400">Perihal: <span id="viewPerihal" class="text-white"></span></p>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">No Agenda</label>
                <div id="viewNoDispo" class="w-full ops-input rounded-lg px-4 py-3 text-white font-mono"></div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Instruksi KABAG</label>
                <div id="viewInstruksiKabag" class="w-full ops-input rounded-lg px-4 py-3 text-white whitespace-pre-line"></div>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Instruksi KASUBBAG</label>
                <div id="viewInstruksiKasubag" class="w-full ops-input rounded-lg px-4 py-3 text-white whitespace-pre-line"></div>
            </div>
            <div class="pt-2 flex justify-end">
                <button type="button" class="btn-close-detail px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm transition-colors">Tutup Dokumen</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL TINDAK LANJUT SURAT MASUK (Anggota + View-only Kasubbag) ===== --}}
<div id="tindakLanjutModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="glass-card w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="tlModalContent">
        <div class="p-6 border-b border-ops-border flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                <i class="fas fa-tasks text-green-400"></i>
                <span>Tindak Lanjut Surat</span>
            </h3>
            <button type="button" class="btn-close-tl text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
            {{-- Info Surat --}}
            <div class="space-y-1 bg-ops-abyss/40 p-4 rounded-xl border border-ops-border/50 text-xs">
                <p class="text-gray-400">No. Surat: <span id="tlNoSurat" class="text-white font-semibold font-mono"></span></p>
                <p class="text-gray-400">Perihal: <span id="tlPerihal" class="text-white"></span></p>
            </div>

            {{-- Existing Tindak Lanjut (readonly for all, shows current state) --}}
            <div id="tlExistingArea" class="hidden">
                <p class="section-eyebrow mb-2" style="color: rgba(74,222,128,0.7);">Status Tindak Lanjut Saat Ini</p>
                <div id="tlExistingContent" class="bg-ops-abyss/40 p-4 rounded-xl border border-green-500/20 space-y-2 text-sm"></div>
            </div>

            {{-- Form Input (hanya role anggota) --}}
            @if(auth()->user()->role === 'anggota')
            <form id="tindakLanjutForm" class="space-y-4 border-t border-ops-border pt-4">
                <input type="hidden" id="tlSuratId">
                <p class="section-eyebrow" style="color: rgba(74,222,128,0.7);">Isi / Edit Tindak Lanjut</p>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Jenis Aksi</label>
                    <div class="relative">
                        <select id="tl_tipe_aksi" name="tipe_aksi" required class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none appearance-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih Jenis Aksi --</option>
                            <option value="tindak_lanjut">Tindak Lanjut</option>
                            <option value="arsip">Arsipkan</option>
                            <option value="buat_balasan">Buat Balasan Surat</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-ops-gold">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                <div id="tlNoBalasanWrap" class="hidden">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">No. Surat Balasan</label>
                    <input type="text" id="tl_no_balasan" name="no_balasan" class="w-full ops-input rounded-lg px-4 py-3 text-sm text-white focus:outline-none" placeholder="Nomor surat balasan (opsional)">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Catatan Tindak Lanjut <span class="text-red-400">*</span></label>
                    <textarea id="tl_catatan" name="catatan" rows="4" required class="w-full ops-input rounded-lg px-4 py-3 text-sm text-white focus:outline-none" placeholder="Tuliskan aksi yang dilakukan, kapan (hari/tanggal/jam), dan detail tindak lanjut..."></textarea>
                </div>
                <div class="pt-2 flex justify-end space-x-3">
                    <button type="button" class="btn-close-tl px-5 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white border border-ops-border transition-colors">Batal</button>
                    <button type="submit" id="btnSubmitTL" class="px-5 py-2.5 bg-green-600 hover:bg-green-500 text-white font-bold rounded-lg text-sm flex items-center space-x-2 transition-all shadow-lg">
                        <i class="fas fa-check text-xs"></i> <span>Simpan Tindak Lanjut</span>
                    </button>
                </div>
            </form>
            @endif

            {{-- Tombol tutup (untuk kasubbag/view-only/urmin) --}}
            <div id="tlCloseOnlyWrap" class="pt-2 flex justify-end @if(auth()->user()->role === 'anggota') hidden @endif">
                <button type="button" class="btn-close-tl px-5 py-2.5 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-xl text-sm transition-colors">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let currentSubbagFilter = null;
    let currentPicFilter = null;
    const currentUserRole = "{{ auth()->user()->role }}";
    const currentUserSubbag = "{{ auth()->user()->subbag }}";
    const canDisposisi = {{ auth()->user()->canDisposisi() ? 'true' : 'false' }};
    const canSeeAll = {{ auth()->user()->canSeeAllSubbag() ? 'true' : 'false' }};
    const isAnggota = currentUserRole === 'anggota';
    const isKasubbag = currentUserRole === 'kasubbag';

    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        const urlParams = new URLSearchParams(window.location.search);
        const statusFilter = urlParams.get('status');
        if (statusFilter) window.currentStatusFilter = statusFilter;

        fetchSuratMasuk(currentPage);

        // Auto-open disposisi modal via URL param
        let autoDispoId = urlParams.get('autodispo');
        if (autoDispoId && canDisposisi) {
            let finalNoSurat = urlParams.get('no_surat') ? decodeURIComponent(urlParams.get('no_surat')) : 'Arsip/' + autoDispoId;
            let finalPerihal = urlParams.get('perihal') ? decodeURIComponent(urlParams.get('perihal')) : 'Menunggu Instruksi';
            setTimeout(() => { triggerDispoModal(autoDispoId, finalNoSurat, finalPerihal); }, 150);
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        // Tombol Beri Disposisi
        $(document).on('click', '.btn-open-dispo', function() {
            let id = $(this).data('id');
            let no = decodeURIComponent($(this).data('no') || '');
            let perihal = decodeURIComponent($(this).data('perihal') || '');
            triggerDispoModal(id, no, perihal);
        });

        // Tombol Lihat Nota
        $(document).on('click', '.btn-open-detail', function() {
            let no     = decodeURIComponent($(this).data('no') || '');
            let perihal= decodeURIComponent($(this).data('perihal') || '');
            let dispo  = decodeURIComponent($(this).data('dispo') || '-');
            let kabag  = decodeURIComponent($(this).data('kabag') || '-');
            let kasubag= decodeURIComponent($(this).data('kasubag') || '-');
            $('#viewNoSurat').text(no);
            $('#viewPerihal').text(perihal);
            $('#viewNoDispo').text(dispo);
            $('#viewInstruksiKabag').text(kabag);
            $('#viewInstruksiKasubag').text(kasubag);
            $('#detailDisposisiModal').removeClass('hidden').addClass('flex');
            setTimeout(() => { $('#detailDispoContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'); }, 50);
        });

        $(document).on('click', '.btn-close-dispo', function() {
            $('#dispoModalContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
            setTimeout(() => { $('#disposisiModal').removeClass('flex').addClass('hidden'); }, 300);
        });
        $(document).on('click', '.btn-close-detail', function() {
            $('#detailDispoContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
            setTimeout(() => { $('#detailDisposisiModal').removeClass('flex').addClass('hidden'); }, 300);
        });

        // Scan AI
        if ($('#btnAutoScan').length) {
            $('#btnAutoScan').on('click', function(e) {
                e.preventDefault();
                let fileInput = $('#file_pdf')[0].files[0];
                if (!fileInput) {
                    Swal.fire({ icon: 'warning', title: 'File Kosong', text: 'Upload PDF terlebih dahulu.', background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#00c6ff' });
                    return;
                }
                let formData = new FormData();
                formData.append('file_pdf', fileInput);
                Swal.fire({ title: 'Menganalisis Dokumen', html: 'AI sedang mengekstrak data...', background: '#0b1628', color: '#F3F4F6', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.ajax({
                    url: '/api/surat-masuk/parse', type: 'POST', data: formData, contentType: false, processData: false,
                    success: function(response) {
                        Swal.close();
                        if (response.status === 200) {
                            const d = new Date();
                            const localDate = new Date(d.getTime() - (d.getTimezoneOffset() * 60000));
                            $('#no_surat').val(response.data.no_surat);
                            $('#tanggal_masuk').val(localDate.toISOString().split('T')[0]);
                            $('#dari').val(response.data.dari);
                            $('#kepada').val(response.data.kepada);
                            $('#perihal').val(response.data.perihal);
                            Swal.fire({ icon: 'success', title: 'Scan Selesai!', text: 'Data berhasil diekstrak.', background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#00c6ff' });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
                        Swal.fire({ icon: 'error', title: 'Gagal Scan', text: msg, background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#ef4444' });
                    }
                });
            });
        }

        // Submit Tambah Surat
        if ($('#formTambahSurat').length) {
            $('#formTambahSurat').on('submit', function(e) {
                e.preventDefault();
                const checked = $('input.subbag-checkbox:checked').length;
                if (checked === 0) {
                    Swal.fire({ icon: 'warning', title: 'Subbag Tujuan Kosong', text: 'Pilih minimal 1 subbag tujuan.', background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#00c6ff' });
                    return;
                }
                let formData = new FormData(this);
                $('#btnSubmitTambah').prop('disabled', true).text('Menyimpan...');
                $.ajax({
                    url: "{{ url('/api/surat-masuk') }}", type: "POST", data: formData, contentType: false, processData: false,
                    success: function(res) {
                        closeModal('modalTambah');
                        $('#formTambahSurat')[0].reset();
                        $('input.subbag-checkbox').prop('checked', false);
                        Swal.fire({ icon: 'success', title: 'Sukses', text: res.message, background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#00c6ff' });
                        fetchSuratMasuk(1);
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Gagal menyimpan arsip.';
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#ef4444' });
                    },
                    complete: function() { $('#btnSubmitTambah').prop('disabled', false).text('Simpan Arsip'); }
                });
            });
        }

        // Submit Disposisi
        if ($('#disposisiForm').length) {
            $('#disposisiForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#dispoSuratId').val();
                
                // Append PIC to catatan kasubbag if exists
                let formData = new URLSearchParams($(this).serialize());
                let pic = formData.get('disposisi_pic');
                if (pic) {
                    let cat = formData.get('disposisi_kasubag') || '';
                    formData.set('disposisi_kasubag', `[PIC: ${pic}]\n${cat}`);
                }
                
                $('#btnSubmitDisposisi').prop('disabled', true);
                $.ajax({
                    url: `{{ url('/api/surat-masuk/update') }}/${id}`, type: "POST", data: formData.toString(),
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: 'Sukses!', text: res.message, background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#00c6ff' });
                        $('.btn-close-dispo').click();
                        $('#disposisiForm')[0].reset();
                        fetchSuratMasuk(currentPage);
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal memproses.';
                        Swal.fire({ icon: 'error', title: 'Aksi Gagal', text: msg, background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#ef4444' });
                    },
                    complete: function() { $('#btnSubmitDisposisi').prop('disabled', false); }
                });
            });
        }

        $(document).on('change', '#picFilter', function() {
            setPicFilter($(this).val() || null);
        });

        // Drag & drop
        if (document.getElementById('drop-area')) {
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('file_pdf');
            const fileInfo = document.getElementById('file-info');
            const fileNameDisplay = document.getElementById('file-name');
            const fileSizeDisplay = document.getElementById('file-size');

            ['dragenter','dragover','dragleave','drop'].forEach(ev => dropArea.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false));
            ['dragenter','dragover'].forEach(ev => dropArea.addEventListener(ev, () => dropArea.classList.add('border-ops-gold','bg-white/5')));
            ['dragleave','drop'].forEach(ev => dropArea.addEventListener(ev, () => dropArea.classList.remove('border-ops-gold','bg-white/5')));
            dropArea.addEventListener('drop', e => { fileInput.files = e.dataTransfer.files; handleFileDrop(e.dataTransfer.files, fileInfo, fileNameDisplay, fileSizeDisplay, fileInput); });
            fileInput.addEventListener('change', function() { handleFileDrop(this.files, fileInfo, fileNameDisplay, fileSizeDisplay, fileInput); });
        }
    });

    function handleFileDrop(files, fileInfo, fileNameDisplay, fileSizeDisplay, fileInput) {
        const file = files[0];
        if (!file) return;
        if (file.type !== 'application/pdf') {
            Swal.fire('Error', 'Hanya file PDF!', 'error');
            fileInput.value = '';
            fileInfo.classList.add('hidden');
            return;
        }
        fileInfo.classList.remove('hidden');
        fileNameDisplay.textContent = file.name;
        fileSizeDisplay.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    }

    function validateSubbagCheckboxes(changed) {
        const checked = $('input.subbag-checkbox:checked');
        if (checked.length > 2) {
            changed.checked = false;
            $('#subbagWarning').removeClass('hidden');
            setTimeout(() => $('#subbagWarning').addClass('hidden'), 3000);
        }
    }

    function setSubbagFilter(subbag) {
        currentSubbagFilter = subbag;
        // Update button styles
        $('.subbag-filter-btn').removeClass('bg-ops-cyan/20 border-ops-cyan/40 text-ops-cyan').addClass('border-ops-border/40 text-slate-400');
        let btnId = subbag ? '#f' + subbag : '#fAll';
        $(btnId).addClass('bg-ops-cyan/20 border-ops-cyan/40 text-ops-cyan').removeClass('border-ops-border/40 text-slate-400');
        fetchSuratMasuk(1);
    }

    function setPicFilter(pic) {
        currentPicFilter = pic;
        $('.pic-filter-btn').removeClass('bg-ops-gold/20 border-ops-gold/40 text-ops-gold').addClass('border-ops-border/40 text-slate-400');
        if (!pic) {
            $('#picAll').addClass('bg-ops-gold/20 border-ops-gold/40 text-ops-gold').removeClass('border-ops-border/40 text-slate-400');
        } else {
            $('.pic-filter-btn').each(function() {
                if ($(this).text().trim() === pic) {
                    $(this).addClass('bg-ops-gold/20 border-ops-gold/40 text-ops-gold').removeClass('border-ops-border/40 text-slate-400');
                }
            });
        }
        $('#picFilter').val(pic || '');
        fetchSuratMasuk(1);
    }

    function triggerDispoModal(id, no, perihal) {
        $('#dispoSuratId').val(id);
        $('#textNoSurat').text(no);
        $('#textPerihal').text(perihal);
        $('#disposisi_kabag').val('');
        $('#disposisi_kasubag').val('');
        $('#disposisiModal').removeClass('hidden').addClass('flex');
        setTimeout(() => { $('#dispoModalContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'); }, 50);
    }

    function fetchSuratMasuk(page) {
        currentPage = page;
        let reqData = {
            page: page,
            search: $('#searchFilter').val(),
            start_date: $('#startDateFilter').val(),
            end_date: $('#endDateFilter').val()
        };
        if (window.currentStatusFilter) reqData.status = window.currentStatusFilter;
        if (currentSubbagFilter) reqData.subbag = currentSubbagFilter;
        if (currentPicFilter) reqData.pic = currentPicFilter;

        $.ajax({
            url: "{{ url('/api/surat-masuk') }}", type: "GET", data: reqData, dataType: "json",
            success: function(res) { renderTable(res.data); renderPagination(res.pagination); }
        });
    }

    function renderTable(data) {
        let html = '';
        if (!data || data.length === 0) {
            $('#tableBody').html('<tr><td colspan="7" class="text-center py-8 text-gray-500"><i class="fas fa-inbox text-2xl mb-2 block opacity-30"></i>Tidak ada arsip ditemukan.</td></tr>');
            return;
        }

        data.forEach(row => {
            let badgeColor = row.status === 'pending'
                ? 'stamp stamp-pending inline-flex items-center gap-1.5'
                : 'stamp stamp-disposisi inline-flex items-center gap-1.5';
            let statusDot = row.status === 'pending'
                ? '<span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>'
                : '<span class="h-1.5 w-1.5 rounded-full bg-ops-cyan"></span>';

            let safeNoSurat = encodeURIComponent(row.no_surat || '');
            let safePerihal = encodeURIComponent(row.perihal || '');
            let tombolAksi = '';

            // Subbag tujuan badge
            let subbagBadges = '';
            if (canSeeAll && row.subbags && row.subbags.length > 0) {
                subbagBadges = row.subbags.map(s => `<span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-ops-violet/15 border border-ops-violet/30 text-ops-violet uppercase">${s.subbag}</span>`).join(' ');
            }

            // Validasi hak disposisi per baris:
            // Admin/Kabag: bisa disposisi semua surat
            // Kasubbag: HANYA bisa disposisi surat yang ditujukan ke subbag miliknya (Kasubbag Urmin hanya bisa pantau surat subbag lain)
            let isAllowedToDispoThisRow = false;
            if (currentUserRole === 'admin' || currentUserRole === 'kabag') {
                isAllowedToDispoThisRow = true;
            } else if (currentUserRole === 'kasubbag') {
                if (row.subbags && row.subbags.some(s => s.subbag === currentUserSubbag)) {
                    isAllowedToDispoThisRow = true;
                }
            }

            if (row.status === 'pending') {
                if (isAllowedToDispoThisRow) {
                    tombolAksi = `<button type="button" class="btn-open-dispo px-3 py-1.5 border border-amber-500/40 text-amber-400 bg-amber-500/08 text-xs rounded-lg font-semibold transition-all flex items-center space-x-1 hover:bg-amber-500/15" data-id="${row.id}" data-no="${safeNoSurat}" data-perihal="${safePerihal}"><i class="fas fa-file-signature text-[10px]"></i><span>Beri Disposisi</span></button>`;
                } else if (canSeeAll) {
                    tombolAksi = `<span class="text-xs text-slate-500 italic">Hanya Memantau</span>`;
                } else {
                    tombolAksi = `<span class="text-xs text-gray-500 italic">Menunggu Disposisi</span>`;
                }
            } else {
                let safeDispo = encodeURIComponent(row.no_dispo || '-');
                let safeKabag = encodeURIComponent(row.disposisi_kabag || '-');
                let safeKasubag = encodeURIComponent(row.disposisi_kasubag || '-');
                tombolAksi = `<button type="button" class="btn-open-detail px-3 py-1.5 border border-ops-cyan/30 text-ops-cyan bg-ops-cyan/08 text-xs rounded-lg font-semibold transition-all flex items-center space-x-1 hover:bg-ops-cyan/15" data-no="${safeNoSurat}" data-perihal="${safePerihal}" data-dispo="${safeDispo}" data-kabag="${safeKabag}" data-kasubag="${safeKasubag}"><i class="fas fa-eye text-[10px]"></i><span>Lihat Nota</span></button>`;

                // Tombol Tindak Lanjut:
                // 1. Anggota & Kasubbag subbag tujuan
                // 2. Urmin (kasubbag maupun anggota) & Admin bisa melihat hasil tinjut subbag lain
                let isDestSubbag = row.subbags && row.subbags.some(s => s.subbag === currentUserSubbag);
                let isDestMember = isAnggota && isDestSubbag;
                let canTL = isDestSubbag || canSeeAll || currentUserSubbag === 'urmin';

                if (canTL) {
                    let hasTL = !!row.tindak_lanjut;
                    let tlColor = hasTL
                        ? 'border-green-500/40 text-green-400 bg-green-500/08 hover:bg-green-500/15'
                        : (isDestMember ? 'border-amber-500/40 text-amber-400 bg-amber-500/08 hover:bg-amber-500/15' : 'border-slate-600/40 text-slate-400 bg-slate-500/08 hover:bg-slate-500/15');
                    let tlIcon = hasTL ? 'fa-check-circle' : 'fa-edit';
                    let tlLabel = hasTL ? 'Tinjut ✓' : (isDestMember ? 'Tindak Lanjut' : 'Cek Tinjut');
                    tombolAksi += ` <button type="button" class="btn-open-tl px-3 py-1.5 border ${tlColor} text-xs rounded-lg font-semibold transition-all flex items-center space-x-1" data-id="${row.id}" data-no="${safeNoSurat}" data-perihal="${safePerihal}" data-dest="${isDestMember ? '1' : '0'}"><i class="fas ${tlIcon} text-[10px]"></i><span>${tlLabel}</span></button>`;
                }
            }

            let tlInfo = '';
            if (row.tindak_lanjut) {
                let tl = row.tindak_lanjut;
                let tipeMap = { tindak_lanjut: 'Tindak Lanjut', arsip: 'Diarsipkan', buat_balasan: 'Buat Balasan', lainnya: 'Lainnya' };
                let tipeLabel = tipeMap[tl.tipe_aksi] || tl.tipe_aksi;
                let waktu = tl.updated_at || tl.created_at || '';
                let userNama = tl.nama_user ? ` • ${tl.nama_user}` : '';
                let safeCatatan = (tl.catatan || '').replace(/"/g, '&quot;');
                tlInfo = `<div class="mt-1 flex items-center gap-1 text-[10px] text-emerald-400 font-mono tracking-tight" title="${safeCatatan}">
                    <i class="fas fa-check-double text-[9px] text-emerald-400"></i>
                    <span>${tipeLabel} <span class="text-slate-400">(${waktu}${userNama})</span></span>
                </div>`;
            }

            let picBadge = row.pic
                ? `<div class="mt-1"><span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-ops-gold/10 border border-ops-gold/30 text-ops-gold hover:bg-ops-gold/20 transition-all cursor-pointer" onclick="setPicFilter('${row.pic}')" title="Klik untuk filter PIC: ${row.pic}"><i class="fas fa-user-tag text-[8px]"></i>${row.pic}</span></div>`
                : '';

            let fileButton = row.file_pdf
                ? `<button type="button" onclick="openPdfModal('{{ url('/arsip/dokumen') }}/${row.file_pdf}', '${row.file_pdf}')" class="text-ops-gold hover:text-white transition-colors" title="Preview PDF"><i class="fas fa-file-pdf text-base"></i></button>`
                : `<span class="text-gray-600">-</span>`;

            let subbagCol = canSeeAll ? `<td class="py-3.5 px-6">${subbagBadges || '-'}</td>` : '';

            html += `<tr class="hover:bg-white/[0.02] transition-colors">
                <td class="py-3.5 px-6 font-mono text-xs"><span class="text-white">${row.no_surat}</span>${picBadge}</td>
                <td class="py-3.5 px-6 text-gray-300 text-xs">${row.dari}</td>
                <td class="py-3.5 px-6 text-gray-300 max-w-xs truncate text-xs">${row.perihal}</td>
                <td class="py-3.5 px-6 text-gray-400 font-mono text-xs">${row.tanggal_masuk}</td>
                ${subbagCol}
                <td class="py-3.5 px-6"><span class="${badgeColor}">${statusDot}${row.status}</span>${tlInfo}</td>
                <td class="py-3.5 px-6 text-center flex items-center justify-center space-x-3">${fileButton} ${tombolAksi}</td>
            </tr>`;
        });
        $('#tableBody').html(html);
    }

    function renderPagination(meta) {
        $('#paginationInfo').text(`Halaman ${meta.page} dari ${meta.total_pages}`);
        let html = `<button onclick="fetchSuratMasuk(${meta.page - 1})" ${meta.page === 1 ? 'disabled' : ''} class="px-3 py-1.5 glass-card border-ops-border hover:bg-white/[0.05] disabled:opacity-40 text-xs rounded-lg text-white transition-colors">Prev</button>`;
        html += `<button onclick="fetchSuratMasuk(${meta.page + 1})" ${meta.page === meta.total_pages || meta.total_pages === 0 ? 'disabled' : ''} class="px-3 py-1.5 glass-card border-ops-border hover:bg-white/[0.05] disabled:opacity-40 text-xs rounded-lg text-white transition-colors">Next</button>`;
        $('#paginationButtons').html(html);
    }

    function handleFilter() {
        let picVal = $('#picFilter').val();
        currentPicFilter = picVal ? picVal : null;
        $('.pic-filter-btn').removeClass('bg-ops-gold/20 border-ops-gold/40 text-ops-gold').addClass('border-ops-border/40 text-slate-400');
        if (!currentPicFilter) {
            $('#picAll').addClass('bg-ops-gold/20 border-ops-gold/40 text-ops-gold').removeClass('border-ops-border/40 text-slate-400');
        } else {
            $('.pic-filter-btn').each(function() {
                if ($(this).text().trim() === currentPicFilter) {
                    $(this).addClass('bg-ops-gold/20 border-ops-gold/40 text-ops-gold').removeClass('border-ops-border/40 text-slate-400');
                }
            });
        }
        window.currentStatusFilter = null;
        fetchSuratMasuk(1);
    }
    function openModal(id) { $(`#${id}`).removeClass('hidden'); }
    function closeModal(id) { $(`#${id}`).addClass('hidden'); }

    function exportCsv() {
        let params = new URLSearchParams({ search: $('#searchFilter').val() || '', start_date: $('#startDateFilter').val() || '', end_date: $('#endDateFilter').val() || '' });
        if (window.currentStatusFilter) params.set('status', window.currentStatusFilter);
        if (currentSubbagFilter) params.set('subbag', currentSubbagFilter);
        if (currentPicFilter) params.set('pic', currentPicFilter);
        window.location.href = "{{ url('/api/surat-masuk/export') }}?" + params.toString();
    }

    // ===== TINDAK LANJUT HANDLERS =====
    $(document).on('click', '.btn-open-tl', function() {
        let id      = $(this).data('id');
        let noSurat = decodeURIComponent($(this).data('no') || '');
        let perihal = decodeURIComponent($(this).data('perihal') || '');
        let isDest  = $(this).data('dest') == '1';
        openTindakLanjutModal(id, noSurat, perihal, isDest);
    });

    $(document).on('click', '.btn-close-tl', function() {
        $('#tlModalContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => { $('#tindakLanjutModal').removeClass('flex').addClass('hidden'); }, 300);
    });

    function openTindakLanjutModal(suratId, noSurat, perihal, isDestMember) {
        $('#tlSuratId').val(suratId);
        $('#tlNoSurat').text(noSurat);
        $('#tlPerihal').text(perihal);
        $('#tlExistingArea').addClass('hidden');
        $('#tlExistingContent').html('');
        if ($('#tindakLanjutForm').length) {
            $('#tindakLanjutForm')[0].reset();
            $('#tlNoBalasanWrap').addClass('hidden');
            if (isDestMember) {
                $('#tindakLanjutForm').removeClass('hidden');
                $('#tlCloseOnlyWrap').addClass('hidden');
            } else {
                $('#tindakLanjutForm').addClass('hidden');
                $('#tlCloseOnlyWrap').removeClass('hidden');
            }
        }

        // Fetch existing tindak lanjut
        $.ajax({
            url: `/api/surat-masuk/${suratId}/tindak-lanjut`,
            type: 'GET',
            success: function(res) {
                if (res.data) {
                    let tl = res.data;
                    let tipeLabels = { tindak_lanjut: 'Tindak Lanjut', arsip: 'Arsipkan', buat_balasan: 'Buat Balasan Surat', lainnya: 'Lainnya' };
                    let html = `<div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-green-400 font-semibold"><i class="fas fa-check-circle mr-1"></i>${tipeLabels[tl.tipe_aksi] || tl.tipe_aksi}</span>
                            <span class="text-[10px] text-gray-500 font-mono">${tl.updated_at || tl.created_at}</span>
                        </div>
                        <p class="text-white text-sm whitespace-pre-line">${tl.catatan}</p>
                        ${tl.no_balasan ? `<p class="text-xs text-ops-gold">No. Balasan: ${tl.no_balasan}</p>` : ''}
                        <p class="text-[10px] text-gray-500">Oleh: ${tl.nama_user}</p>
                    </div>`;
                    $('#tlExistingContent').html(html);
                    $('#tlExistingArea').removeClass('hidden');

                    // Pre-fill form for editing (hanya anggota subbag tujuan)
                    if (isDestMember && $('#tindakLanjutForm').length) {
                        $('#tl_tipe_aksi').val(tl.tipe_aksi);
                        $('#tl_catatan').val(tl.catatan);
                        $('#tl_no_balasan').val(tl.no_balasan || '');
                        if (tl.tipe_aksi === 'buat_balasan') $('#tlNoBalasanWrap').removeClass('hidden');
                        $('#btnSubmitTL span').text('Perbarui Tindak Lanjut');
                    }
                } else {
                    if (!isDestMember) {
                        $('#tlExistingContent').html('<p class="text-xs text-slate-400 italic"><i class="fas fa-info-circle mr-1 text-ops-cyan"></i>Belum ada tindak lanjut yang diinput untuk surat ini.</p>');
                        $('#tlExistingArea').removeClass('hidden');
                    } else if ($('#tindakLanjutForm').length) {
                        $('#btnSubmitTL span').text('Simpan Tindak Lanjut');
                    }
                }
            }
        });

        $('#tindakLanjutModal').removeClass('hidden').addClass('flex');
        setTimeout(() => { $('#tlModalContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'); }, 50);
    }

    // Conditional show no_balasan field
    $(document).on('change', '#tl_tipe_aksi', function() {
        if ($(this).val() === 'buat_balasan') {
            $('#tlNoBalasanWrap').removeClass('hidden');
        } else {
            $('#tlNoBalasanWrap').addClass('hidden');
            $('#tl_no_balasan').val('');
        }
    });

    // Submit tindak lanjut form
    if ($('#tindakLanjutForm').length) {
        $(document).on('submit', '#tindakLanjutForm', function(e) {
            e.preventDefault();
            let suratId = $('#tlSuratId').val();
            let data = {
                tipe_aksi:  $('#tl_tipe_aksi').val(),
                catatan:    $('#tl_catatan').val(),
                no_balasan: $('#tl_no_balasan').val() || null,
            };
            $('#btnSubmitTL').prop('disabled', true);
            $.ajax({
                url: `/api/surat-masuk/${suratId}/tindak-lanjut`,
                type: 'POST',
                data: data,
                success: function(res) {
                    Swal.fire({ icon: 'success', title: 'Sukses!', text: res.message, background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#22c55e' });
                    $('.btn-close-tl').first().click();
                    fetchSuratMasuk(currentPage);
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message || 'Gagal menyimpan tindak lanjut.';
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msg, background: '#0b1628', color: '#F3F4F6', confirmButtonColor: '#ef4444' });
                },
                complete: function() { $('#btnSubmitTL').prop('disabled', false); }
            });
        });
    }

</script>
@endpush