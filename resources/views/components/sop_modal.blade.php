<!-- ===== GLOBAL MODAL PANDUAN PERAN, SOP & DO's/DON'Ts ===== -->
<div id="sopModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md transition-all duration-300">
    <div class="glass w-full max-w-5xl rounded-2xl shadow-2xl border border-ops-border shadow-black/80 flex flex-col overflow-hidden max-h-[90vh]">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-ops-border flex items-center justify-between bg-ops-deep/80 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-ops-gold/15 border border-ops-gold/30 text-ops-gold">
                    <i class="fas fa-book-open text-lg"></i>
                </div>
                <div>
                    <h2 class="font-display text-lg font-bold text-white tracking-wide">Buku Saku SOP &amp; Panduan Peran (Do's &amp; Don'ts)</h2>
                    <p class="section-eyebrow text-slate-400 mt-0.5">STANDAR OPERASIONAL PROSEDUR KEARSIPAN &amp; DISTRIBUSI SURAT DIVHUBINTER POLRI</p>
                </div>
            </div>
            <button onclick="closeSopModal()" class="text-slate-400 hover:text-red-400 p-2 rounded-lg hover:bg-white/5 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="px-6 py-2 border-b border-ops-border bg-ops-abyss/60 flex flex-wrap gap-2 overflow-x-auto flex-shrink-0">
            <button onclick="switchSopTab('tab-alur')" id="btn-tab-alur" class="sop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all bg-ops-cyan/20 border border-ops-cyan/50 text-ops-cyan flex items-center space-x-2">
                <i class="fas fa-project-diagram"></i><span>Alur Global</span>
            </button>
            <button onclick="switchSopTab('tab-urmin')" id="btn-tab-urmin" class="sop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all border border-ops-border text-slate-400 hover:text-white flex items-center space-x-2">
                <i class="fas fa-building"></i><span>URMIN</span>
            </button>
            <button onclick="switchSopTab('tab-kasubbag')" id="btn-tab-kasubbag" class="sop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all border border-ops-border text-slate-400 hover:text-white flex items-center space-x-2">
                <i class="fas fa-user-tie"></i><span>KASUBBAG</span>
            </button>
            <button onclick="switchSopTab('tab-anggota')" id="btn-tab-anggota" class="sop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all border border-ops-border text-slate-400 hover:text-white flex items-center space-x-2">
                <i class="fas fa-user-edit"></i><span>ANGGOTA / KONSEPTOR</span>
            </button>
            <button onclick="switchSopTab('tab-kabag')" id="btn-tab-kabag" class="sop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all border border-ops-border text-slate-400 hover:text-white flex items-center space-x-2">
                <i class="fas fa-stamp"></i><span>KABAG (TTD Harian)</span>
            </button>
            <button onclick="switchSopTab('tab-subbag')" id="btn-tab-subbag" class="sop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all border border-ops-border text-slate-400 hover:text-white flex items-center space-x-2">
                <i class="fas fa-layer-group"></i><span>Karakteristik Subbag</span>
            </button>
            <button onclick="switchSopTab('tab-admin')" id="btn-tab-admin" class="sop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all border border-ops-border text-slate-400 hover:text-white flex items-center space-x-2">
                <i class="fas fa-shield-alt"></i><span>ADMIN</span>
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="p-6 overflow-y-auto space-y-6 text-sm text-slate-300">

            <!-- TAB 1: ALUR GLOBAL -->
            <div id="tab-alur" class="sop-content space-y-6">
                <div class="glass-card p-4 rounded-xl border border-ops-cyan/30">
                    <h3 class="font-display text-white font-bold text-base flex items-center space-x-2">
                        <i class="fas fa-sync-alt text-ops-cyan"></i>
                        <span>Siklus Dokumen Terpadu InterOps-Hub</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Diagram alur surat masuk dan surat keluar berjenjang antar peran di lingkungan Bagian Divhubinter Polri.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Alur Surat Masuk -->
                    <div class="glass-card p-5 rounded-xl border border-ops-border/60 space-y-4">
                        <div class="flex items-center space-x-2 pb-2 border-b border-ops-border">
                            <span class="stamp stamp-pending">INBOUND</span>
                            <h4 class="font-bold text-white">Alur Siklus Surat Masuk (SURMA)</h4>
                        </div>
                        <ol class="relative border-l border-ops-border/80 ml-3 space-y-4 text-xs">
                            <li class="mb-4 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-ops-cyan/20 rounded-full -left-3 ring-4 ring-ops-deep text-ops-cyan font-bold text-[10px]">1</span>
                                <h5 class="font-bold text-white">Penerimaan &amp; Input (URMIN)</h5>
                                <p class="text-slate-400 mt-0.5">Terima surat dari Zimbra, Bag DHI, TAUD, atau Ses. Anggota Urmin menginput ke web &amp; upload PDF.</p>
                            </li>
                            <li class="mb-4 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-ops-cyan/20 rounded-full -left-3 ring-4 ring-ops-deep text-ops-cyan font-bold text-[10px]">2</span>
                                <h5 class="font-bold text-white">Disposisi Awal &amp; Naik Kabag</h5>
                                <p class="text-slate-400 mt-0.5">Disposisi Kaurmin, naik TTD/arahan Kabag untuk surat-surat krusial.</p>
                            </li>
                            <li class="mb-4 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-ops-cyan/20 rounded-full -left-3 ring-4 ring-ops-deep text-ops-cyan font-bold text-[10px]">3</span>
                                <h5 class="font-bold text-white">Distribusi ke Subbag (Bisa 1 atau 2 Subbag)</h5>
                                <p class="text-slate-400 mt-0.5">Urmin menandai subbag tujuan (Koor, BHI, BI, Ops). Surat otomatis muncul di dashboard subbag terkait.</p>
                            </li>
                            <li class="mb-4 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-ops-cyan/20 rounded-full -left-3 ring-4 ring-ops-deep text-ops-cyan font-bold text-[10px]">4</span>
                                <h5 class="font-bold text-white">Disposisi Kasubbag &amp; Pembagian PIC</h5>
                                <p class="text-slate-400 mt-0.5">Kasubbag mengisi instruksi &amp; menunjuk anggota sebagai PIC konseptor, atau menandai 'Arsip Saja' jika hanya info rutin.</p>
                            </li>
                            <li class="ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-emerald-500/20 rounded-full -left-3 ring-4 ring-ops-deep text-emerald-400 font-bold text-[10px]">5</span>
                                <h5 class="font-bold text-emerald-400">Eksekusi oleh Anggota / PIC</h5>
                                <p class="text-slate-400 mt-0.5">Anggota membaca disposisi, menindaklanjuti, dan membuat konsep surat balasan jika dibutuhkan.</p>
                            </li>
                        </ol>
                    </div>

                    <!-- Alur Surat Keluar -->
                    <div class="glass-card p-5 rounded-xl border border-ops-border/60 space-y-4">
                        <div class="flex items-center space-x-2 pb-2 border-b border-ops-border">
                            <span class="stamp stamp-disposisi">OUTBOUND</span>
                            <h4 class="font-bold text-white">Alur Siklus Surat Keluar (SURKEL)</h4>
                        </div>
                        <ol class="relative border-l border-ops-border/80 ml-3 space-y-4 text-xs">
                            <li class="mb-4 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-ops-violet/20 rounded-full -left-3 ring-4 ring-ops-deep text-ops-violet font-bold text-[10px]">1</span>
                                <h5 class="font-bold text-white">Konseptor (PIC Anggota / Subbag)</h5>
                                <p class="text-slate-400 mt-0.5">PIC membuat konsep surat keluar di web, mengisi peruntukan satker tujuan, upload draft, dan paraf konseptor.</p>
                            </li>
                            <li class="mb-4 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-ops-violet/20 rounded-full -left-3 ring-4 ring-ops-deep text-ops-violet font-bold text-[10px]">2</span>
                                <h5 class="font-bold text-white">Paraf Berjenjang (Pamin / Kasubbag)</h5>
                                <p class="text-slate-400 mt-0.5">Kasubbag memeriksa materi konsep &amp; memberikan paraf persetujuan sebelum diajukan ke Kabag.</p>
                            </li>
                            <li class="mb-4 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-ops-violet/20 rounded-full -left-3 ring-4 ring-ops-deep text-ops-violet font-bold text-[10px]">3</span>
                                <h5 class="font-bold text-white">Paraf / TTD Digital Kabag (1-Klik Harian)</h5>
                                <p class="text-slate-400 mt-0.5">Kabag menyetujui surat keluar. TTD harian otomatis diaplikasikan tanpa perlu upload berulang kali.</p>
                            </li>
                            <li class="mb-4 ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-ops-violet/20 rounded-full -left-3 ring-4 ring-ops-deep text-ops-violet font-bold text-[10px]">4</span>
                                <h5 class="font-bold text-white">Penomoran TAUD &amp; Urmin</h5>
                                <p class="text-slate-400 mt-0.5">Berkas dikirim ke TAUD untuk nomor surat resmi, lalu Urmin mengupdate nomor di web.</p>
                            </li>
                            <li class="ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-emerald-500/20 rounded-full -left-3 ring-4 ring-ops-deep text-emerald-400 font-bold text-[10px]">5</span>
                                <h5 class="font-bold text-emerald-400">Pengiriman &amp; Pengarsipan Final</h5>
                                <p class="text-slate-400 mt-0.5">Urmin upload scan berkas 'Clear', kirim via Zimbra/Kominter, dan simpan arsip fisik di lemari arsip.</p>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- TAB 2: URMIN -->
            <div id="tab-urmin" class="sop-content hidden space-y-5">
                <div class="glass-card p-4 rounded-xl border border-ops-border/60">
                    <span class="stamp stamp-disposisi">ROLE: URMIN</span>
                    <h3 class="font-display text-white font-bold text-lg mt-2">Urusan Administrasi (URMIN)</h3>
                    <p class="text-xs text-slate-400 mt-1">Gerbang utama penerimaan surat masuk, penomoran resmi TAUD, distribusi fisik/digital, serta pengarsipan akhir.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Alur Kerja -->
                    <div class="glass-card p-4 rounded-xl space-y-3">
                        <h4 class="font-bold text-ops-cyan text-sm flex items-center space-x-2">
                            <i class="fas fa-tasks"></i><span>Alur Kerja Surat Masuk (SURMA)</span>
                        </h4>
                        <ul class="text-xs space-y-2 text-slate-300 list-disc list-inside">
                            <li>Terima surat masuk dari <strong>Zimbra, Bag DHI, TAUD, atau Ses</strong>.</li>
                            <li>Input metadata surat masuk di web, upload file PDF, dan set disposisi awal.</li>
                            <li>Taruh berkas fisik di meja Kaurmin &amp; naikkan TTD Kabag.</li>
                            <li>Update status surat di web dan pilih <strong>Distribusi Subbag (Koor / BHI / BI / Ops)</strong> (bisa pilih 1 atau 2 subbag).</li>
                        </ul>
                    </div>
                    <div class="glass-card p-4 rounded-xl space-y-3">
                        <h4 class="font-bold text-ops-violet text-sm flex items-center space-x-2">
                            <i class="fas fa-paper-plane"></i><span>Alur Kerja Surat Keluar (SURKEL)</span>
                        </h4>
                        <ul class="text-xs space-y-2 text-slate-300 list-disc list-inside">
                            <li>Terima berkas surat keluar dari subbag (sudah diparaf konseptor, kasubbag, &amp; kabag).</li>
                            <li>Input/distribusikan berkas ke TAUD untuk permintaan <strong>Nomor Surat Resmi</strong>.</li>
                            <li>Setelah nomor terbit, update nomor surat di web.</li>
                            <li>Pisahkan dan upload berkas: <strong>Draft Konseptor</strong> dan <strong>Scan Clear (Final TTD)</strong>.</li>
                            <li>Kirim surat clear lewat <strong>Zimbra / Kominter / Bagian DHI lain</strong>.</li>
                            <li>Simpan arsip digital di web &amp; arsip fisik di lemari penyimpanan.</li>
                        </ul>
                    </div>
                </div>

                <!-- DO & DONT -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 space-y-2">
                        <h4 class="font-bold text-emerald-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-check-circle"></i><span>DO's (Wajib Dilakukan)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>✅ Input surat masuk secepatnya setelah diterima agar SLA tracking tidak merah.</li>
                            <li>✅ Tentukan subbag tujuan secara akurat (pilih 2 subbag jika surat melibatkan koordinasi bersama).</li>
                            <li>✅ Pastikan berkas scan yang diunggah terbaca jelas (OCR-ready).</li>
                            <li>✅ Selalu catat nomor surat dari TAUD sebelum berkas fisik dikirimkan.</li>
                            <li>✅ Arsipkan berkas fisik ke ordner/lemari arsip sesuai nomor urut.</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 space-y-2">
                        <h4 class="font-bold text-red-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-ban"></i><span>DON'Ts (Larangan Keras)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>❌ JANGAN menginput surat masuk tanpa memilih subbag tujuan.</li>
                            <li>❌ JANGAN mengirimkan surat keluar ke instansi luar sebelum mendapat paraf Kabag &amp; nomor TAUD.</li>
                            <li>❌ JANGAN menumpuk berkas fisik di meja tanpa diinput ke dalam sistem kearsipan.</li>
                            <li>❌ JANGAN mencampuradukkan berkas draft konseptor dengan berkas clear ber-TTD.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TAB 3: KASUBBAG -->
            <div id="tab-kasubbag" class="sop-content hidden space-y-5">
                <div class="glass-card p-4 rounded-xl border border-ops-border/60">
                    <span class="stamp stamp-pending">ROLE: KASUBBAG</span>
                    <h3 class="font-display text-white font-bold text-lg mt-2">Kepala Sub Bagian (KASUBBAG)</h3>
                    <p class="text-xs text-slate-400 mt-1">Pengendali teknis operasional subbag: verifikasi urgensi surat masuk, instruksi disposisi, pembagian PIC anggota, dan verifikasi konsep surat keluar.</p>
                </div>

                <div class="glass-card p-4 rounded-xl space-y-3">
                    <h4 class="font-bold text-ops-cyan text-sm flex items-center space-x-2">
                        <i class="fas fa-clipboard-list"></i><span>Prosedur Disposisi &amp; Batasan Subbag</span>
                    </h4>
                    <ul class="text-xs space-y-2 text-slate-300 list-disc list-inside">
                        <li><strong>Isolasi Hak Akses Kasubbag:</strong> Kasubbag BHI, BI, OPS, dan KOOR hanya dapat melihat surat yang masuk ke subbag mereka masing-masing (tampilan awal langsung terfilter murni surat subbag sendiri).</li>
                        <li><strong>Hak Pantau Kasubbag Urmin:</strong> Kasubbag Urmin dapat memantau / melihat surat seluruh subbag, namun <strong>DILARANG / TIDAK BISA</strong> memberikan disposisi pada surat subbag lain (hanya berhak mendisposisi surat yang memang ditujukan ke Urmin).</li>
                        <li><strong>Prosedur Disposisi:</strong> Klik tombol <strong>Beri Disposisi</strong> pada baris surat pending milik subbag Anda.</li>
                        <li><strong>Pilih Instruksi &amp; PIC:</strong> Tentukan arahan (Tindak Lanjuti, Siapkan Konsep Balasan, Hadiri, atau Simpan Arsip) dan isi nama anggota yang ditugaskan sebagai PIC konseptor.</li>
                        <li><strong>Surat Informasi / Agenda Lama:</strong> Jika surat hanya berupa pemberitahuan rutin tanpa perlu surat balasan, pilih arahan <em>'Simpan / Diarsipkan Saja'</em>.</li>
                    </ul>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 space-y-2">
                        <h4 class="font-bold text-emerald-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-check-circle"></i><span>DO's (Wajib Dilakukan)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>✅ Disposisikan surat masuk subbag sendiri dalam waktu &lt; 3 hari agar tidak terkena peringatan SLA Breach.</li>
                            <li>✅ Tentukan PIC anggota pelaksana secara jelas pada setiap lembar disposisi.</li>
                            <li>✅ Periksa dan koreksi konsep surat keluar yang dibuat oleh PIC sebelum diajukan ke Kabag.</li>
                            <li>✅ Bubuhkan paraf Kasubbag pada draft konsep yang sudah valid.</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 space-y-2">
                        <h4 class="font-bold text-red-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-ban"></i><span>DON'Ts (Larangan Keras)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>❌ JANGAN mencoba mendisposisi surat milik subbag lain (Kasubbag Urmin hanya berwenang memantau).</li>
                            <li>❌ JANGAN membiarkan surat tertahan di antrean disposisi tanpa tindak lanjut.</li>
                            <li>❌ JANGAN mengajukan konsep surat keluar ke meja Kabag tanpa paraf Kasubbag.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TAB 4: ANGGOTA / KONSEPTOR -->
            <div id="tab-anggota" class="sop-content hidden space-y-5">
                <div class="glass-card p-4 rounded-xl border border-ops-border/60">
                    <span class="stamp stamp-gold">ROLE: ANGGOTA / KONSEPTOR</span>
                    <h3 class="font-display text-white font-bold text-lg mt-2">Anggota Subbag &amp; Konseptor</h3>
                    <p class="text-xs text-slate-400 mt-1">Pelaksana teknis surat masuk, pembuatan draf konsep surat keluar, pengisian rincian peruntukan instansi tujuan, dan paraf konseptor.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="glass-card p-4 rounded-xl space-y-3">
                        <h4 class="font-bold text-ops-cyan text-sm flex items-center space-x-2">
                            <i class="fas fa-inbox"></i><span>Tindak Lanjut Surat Masuk</span>
                        </h4>
                        <ul class="text-xs space-y-2 text-slate-300 list-disc list-inside">
                            <li>Buka daftar surat masuk subbag Anda setiap hari.</li>
                            <li>Cek kolom <strong>Disposisi &amp; PIC</strong> dari Kasubbag.</li>
                            <li>Jika ditunjuk sebagai PIC, segera siapkan berkas bahan atau materi tanggapan.</li>
                        </ul>
                    </div>
                    <div class="glass-card p-4 rounded-xl space-y-3">
                        <h4 class="font-bold text-ops-violet text-sm flex items-center space-x-2">
                            <i class="fas fa-file-signature"></i><span>Pembuatan Konsep Surat Keluar</span>
                        </h4>
                        <ul class="text-xs space-y-2 text-slate-300 list-disc list-inside">
                            <li>Klik <strong>Tambah Surat Keluar</strong> pada modul Surat Keluar.</li>
                            <li>Isi rincian perihal, instansi/satker tujuan, dan <strong>Catatan Peruntukan Surat</strong>.</li>
                            <li>Upload file draft dokumen (PDF).</li>
                            <li>Minta paraf konseptor (PIC) &amp; ajukan ke Kasubbag.</li>
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 space-y-2">
                        <h4 class="font-bold text-emerald-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-check-circle"></i><span>DO's (Wajib Dilakukan)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>✅ Selalu isi kolom keterangan peruntukan surat (misal: ke Satker Polda X / Bag DHI / NCB Interpol untuk operasi Y).</li>
                            <li>✅ Koordinasikan konsep surat dengan Kasubbag sebelum difinalisasi.</li>
                            <li>✅ Cantumkan identitas konseptor pada berkas draf yang diajukan.</li>
                            <li>✅ Laporkan ke Kasubbag bila tugas tindak lanjut surat telah selesai.</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 space-y-2">
                        <h4 class="font-bold text-red-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-ban"></i><span>DON'Ts (Larangan Keras)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>❌ JANGAN membuat surat keluar tanpa mengisi catatan peruntukan dan tujuan yang jelas.</li>
                            <li>❌ JANGAN mengabaikan disposisi yang sudah dialokasikan ke Anda.</li>
                            <li>❌ JANGAN membagikan draft rahasia ke pihak luar sebelum disetujui Kabag.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TAB 5: KABAG -->
            <div id="tab-kabag" class="sop-content hidden space-y-5">
                <div class="glass-card p-4 rounded-xl border border-ops-border/60">
                    <span class="stamp stamp-red">ROLE: KABAG</span>
                    <h3 class="font-display text-white font-bold text-lg mt-2">Kepala Bagian (KABAG)</h3>
                    <p class="text-xs text-slate-400 mt-1">Pimpinan tingkat Bagian: pemantauan SLA komando, arahan pimpinan pada surat masuk krusial, dan persetujuan/paraf harian 1-klik untuk seluruh surat keluar.</p>
                </div>

                <div class="glass-card p-4 rounded-xl space-y-3">
                    <h4 class="font-bold text-ops-cyan text-sm flex items-center space-x-2">
                        <i class="fas fa-signature"></i><span>Fitur Tanda Tangan / Paraf Harian 1-Klik</span>
                    </h4>
                    <p class="text-xs text-slate-300">
                        Untuk mencegah kelelahan membubuhkan tanda tangan manual berulang kali, sistem menyediakan fitur <strong>Daily Reusable Signature</strong>:
                    </p>
                    <ul class="text-xs space-y-2 text-slate-300 list-disc list-inside">
                        <li>Kabag cukup mengunggah/mengonfirmasi spesimen tanda tangan <strong>1 kali di awal hari</strong>.</li>
                        <li>Untuk surat-surat keluar berikutnya pada hari yang sama, Kabag cukup menekan tombol <strong>Setujui / Paraf</strong> (1-klik langsung diaplikasikan).</li>
                        <li>Tanda tangan otomatis tersimpan aman di sistem dan terikat pada tanggal persetujuan.</li>
                    </ul>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 space-y-2">
                        <h4 class="font-bold text-emerald-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-check-circle"></i><span>DO's (Wajib Dilakukan)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>✅ Monitor tabel <em>Antrean Prioritas Tinggi (SLA &gt;3 Hari)</em> di Dashboard setiap pagi.</li>
                            <li>✅ Cek catatan peruntukan dan paraf Kasubbag sebelum memaraf surat keluar.</li>
                            <li>✅ Berikan catatan koreksi pada kolom feedback jika surat keluar ditolak untuk direvisi konseptor.</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 space-y-2">
                        <h4 class="font-bold text-red-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-ban"></i><span>DON'Ts (Larangan Keras)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>❌ JANGAN membiarkan surat berstatus SLA Breach tanpa keputusan disposisi pimpinan.</li>
                            <li>❌ JANGAN memaraf berkas yang belum memiliki catatan peruntukan dan persetujuan Kasubbag.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TAB 6: KARAKTERISTIK SUBBAG -->
            <div id="tab-subbag" class="sop-content hidden space-y-5">
                <div class="glass-card p-4 rounded-xl border border-ops-border/60">
                    <span class="stamp stamp-disposisi">SUBBAG PROFILES</span>
                    <h3 class="font-display text-white font-bold text-lg mt-2">Gambaran Alur Kerja Spesifik Masing-Masing Subbag</h3>
                    <p class="text-xs text-slate-400 mt-1">Penyesuaian tata kerja internal sesuai karakteristik tugas subbag di lingkungan Bagian Divhubinter Polri.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <!-- KOOR -->
                    <div class="glass-card p-4 rounded-xl border border-ops-cyan/30 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-ops-cyan text-sm">1. Subbag Koordinasi (KOOR)</h4>
                            <span class="stamp stamp-disposisi text-[9px]">KOOR</span>
                        </div>
                        <ul class="space-y-1.5 text-slate-300 list-disc list-inside">
                            <li>Surat diterima dari Urmin sebagai surat masuk KOOR.</li>
                            <li>Bongkar berkas fisik untuk discan &amp; disiapkan lembar disposisi.</li>
                            <li>Kasubbag membagi surat dan menentukan <strong>PIC Konseptor</strong>.</li>
                            <li>PIC bertindak sebagai konseptor, menyusun konsep surat balasan, meminta paraf berjenjang, lalu menyerahkan kembali ke Urmin.</li>
                        </ul>
                    </div>

                    <!-- BHI -->
                    <div class="glass-card p-4 rounded-xl border border-ops-violet/30 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-ops-violet text-sm">2. Subbag Bantuan Hukum Internasional (BHI)</h4>
                            <span class="stamp stamp-pending text-[9px]">BHI</span>
                        </div>
                        <ul class="space-y-1.5 text-slate-300 list-disc list-inside">
                            <li>Surat turun dari Urmin -> Input &amp; verifikasi data surma.</li>
                            <li>Disposisi Kasubbag BHI -> Diteruskan ke anggota.</li>
                            <li>Anggota membagi tugas konsep berkas hukum / MLA / ekstradisi.</li>
                            <li>Surat jadi -> Paraf Konseptor -> Pamin -> Kasubbag -> Kabag -> Naik ke TAUD &amp; Urmin.</li>
                        </ul>
                    </div>

                    <!-- OPS -->
                    <div class="glass-card p-4 rounded-xl border border-emerald-500/30 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-emerald-400 text-sm">3. Subbag Operasi (OPS)</h4>
                            <span class="stamp stamp-disposisi text-[9px]">OPS</span>
                        </div>
                        <ul class="space-y-1.5 text-slate-300 list-disc list-inside">
                            <li>Surat dari Urmin masuk ke modul surat masuk OPS.</li>
                            <li>Disposisi Kasubbag didistribusikan ke anggota subbag Ops.</li>
                            <li><strong>Pemilahan Tipe Surat:</strong> Jika surat follow-up / pemberitahuan kegiatan lama tanpa perlu balasan, cukup ditandai <em>'Diarsipkan'</em>.</li>
                            <li>Jika butuh surat balasan, PIC menjadi konseptor (dapat dibantu staf/magang) &amp; memaraf konsep.</li>
                        </ul>
                    </div>

                    <!-- BI & URMIN -->
                    <div class="glass-card p-4 rounded-xl border border-ops-gold/30 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-ops-gold text-sm">4. Subbag Bilateral (BI) &amp; URMIN</h4>
                            <span class="stamp stamp-gold text-[9px]">BI / URMIN</span>
                        </div>
                        <ul class="space-y-1.5 text-slate-300 list-disc list-inside">
                            <li><strong>Subbag BI:</strong> Menangani dokumen kerja sama kepolisian bilateral &amp; pertukaran informasi antarnegara. Alur disposisi dan paraf mengikuti jalur berjenjang.</li>
                            <li><strong>URMIN:</strong> Pengendali nomor resmi TAUD, portal pengiriman Zimbra/Kominter, dan penjaga fisik lemari arsip.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TAB 7: ADMIN -->
            <div id="tab-admin" class="sop-content hidden space-y-5">
                <div class="glass-card p-4 rounded-xl border border-ops-border/60">
                    <span class="stamp stamp-red">ROLE: ADMIN</span>
                    <h3 class="font-display text-white font-bold text-lg mt-2">Administrator Sistem</h3>
                    <p class="text-xs text-slate-400 mt-1">Supervisi teknis platform, manajemen akun user per subbag, audit log mutasi arsip, dan monitoring integritas SLA.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 space-y-2">
                        <h4 class="font-bold text-emerald-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-check-circle"></i><span>DO's (Wajib Dilakukan)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>✅ Pantau Log Aktivitas secara berkala menggunakan filter subbag untuk audit kendali.</li>
                            <li>✅ Pastikan setiap user terdaftar dengan role (Admin, Kabag, Kasubbag, Anggota) dan subbag (Urmin, BHI, BI, Ops, Koor) yang tepat.</li>
                            <li>✅ Jalankan backup berkas arsip PDF secara berkala.</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 space-y-2">
                        <h4 class="font-bold text-red-400 text-sm flex items-center space-x-2">
                            <i class="fas fa-ban"></i><span>DON'Ts (Larangan Keras)</span>
                        </h4>
                        <ul class="text-xs space-y-1.5 text-slate-300">
                            <li>❌ JANGAN mengubah subbag user aktif tanpa konfirmasi karena akan merubah akses data dokumen yang bersangkutan.</li>
                            <li>❌ JANGAN menghapus riwayat log audit sistem.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 border-t border-ops-border bg-ops-deep/80 flex items-center justify-between flex-shrink-0">
            <p class="text-xs text-slate-500 font-mono">DIVHUBINTER POLRI · INTEROPS-HUB SOP ENGINE</p>
            <button onclick="closeSopModal()" class="px-5 py-2 rounded-lg bg-ops-cyan/20 hover:bg-ops-cyan/30 text-ops-cyan text-xs font-bold transition-all">
                Tutup Panduan
            </button>
        </div>
    </div>
</div>

<script>
    function openSopModal(targetTab) {
        document.getElementById('sopModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (targetTab) {
            let tabId = 'tab-' + targetTab;
            if (document.getElementById(tabId)) {
                switchSopTab(tabId);
            }
        }
    }

    function closeSopModal() {
        document.getElementById('sopModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function switchSopTab(tabId) {
        // Hide all tabs
        document.querySelectorAll('.sop-content').forEach(el => el.classList.add('hidden'));
        // Show target tab
        const target = document.getElementById(tabId);
        if (target) target.classList.remove('hidden');

        // Reset button states
        document.querySelectorAll('.sop-tab-btn').forEach(btn => {
            btn.className = 'sop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all border border-ops-border text-slate-400 hover:text-white flex items-center space-x-2';
        });

        // Highlight active button
        const activeBtn = document.getElementById('btn-' + tabId);
        if (activeBtn) {
            activeBtn.className = 'sop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all bg-ops-cyan/20 border border-ops-cyan/50 text-ops-cyan flex items-center space-x-2';
        }
    }

    // Close on backdrop click
    document.getElementById('sopModal').addEventListener('click', function(e) {
        if (e.target === this) closeSopModal();
    });
</script>
