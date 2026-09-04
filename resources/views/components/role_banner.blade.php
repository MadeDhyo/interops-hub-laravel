<!-- ===== ROLE CONTEXT BANNER (DO & DON'T QUICK GUIDE) ===== -->
@if(Auth::check())
@php
    $userRole = Auth::user()->role;
    $userSubbag = Auth::user()->subbag;
    $subbagUpper = strtoupper($userSubbag ?? '-');
@endphp

<div id="roleContextBanner" class="glass-card bracket-box p-4 rounded-xl border border-ops-border/40 relative overflow-hidden transition-all duration-300 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-start space-x-3">
            <div class="p-2.5 rounded-lg bg-ops-cyan/10 border border-ops-cyan/30 text-ops-cyan mt-0.5">
                <i class="fas fa-id-badge text-lg"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="font-display font-bold text-white text-base">Peran Anda: <span class="text-ops-cyan uppercase">{{ $userRole }}</span> {{ $userSubbag ? '· SUBBAG ' . $subbagUpper : '' }}</span>
                    <span class="stamp stamp-disposisi text-[9px] py-0.5 px-2">Aktif</span>
                </div>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    @if($userRole === 'admin')
                        Mode Pengawasan Total: Akses penuh manajemen personel, log audit per subbag, dan supervisi SLA berkas.
                    @elseif($userRole === 'kabag')
                        Pimpinan Tertinggi Bagian: Disposisi pimpinan &amp; persetujuan/paraf harian 1-klik untuk seluruh surat keluar subbag.
                    @elseif($userRole === 'kasubbag')
                        Kepala Subbag {{ $subbagUpper }}: Pengendali disposisi surat masuk, penunjukan PIC anggota/konseptor, dan verifikasi konsep surat keluar.
                    @elseif($userRole === 'anggota' && $userSubbag === 'urmin')
                        Urusan Administrasi (URMIN): Input surat masuk pusat (Zimbra/Taud/Ses), distribusi antar subbag, input nomor TAUD &amp; pengarsipan berkas clear/konseptor.
                    @elseif($userRole === 'anggota')
                        Pelaksana Subbag {{ $subbagUpper }}: Menindaklanjuti disposisi Kasubbag, bertindak sebagai PIC konseptor surat keluar, paraf konsep, dan koordinasi dengan Urmin.
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2 flex-shrink-0 self-end md:self-center">
            <button onclick="openSopModal('{{ $userRole === 'anggota' && $userSubbag === 'urmin' ? 'urmin' : $userRole }}')" class="px-3.5 py-2 rounded-lg bg-ops-cyan/15 hover:bg-ops-cyan/25 border border-ops-cyan/40 text-ops-cyan text-xs font-bold transition-all flex items-center space-x-2 shadow-sm">
                <i class="fas fa-book-reader"></i>
                <span>Lihat Do's &amp; Don'ts Lengkap</span>
            </button>
        </div>
    </div>

    <!-- Quick Highlights per Role -->
    <div class="mt-3 pt-3 border-t border-ops-border/40 grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
        <div class="bg-emerald-500/5 border border-emerald-500/20 rounded-lg p-2.5 flex items-start space-x-2">
            <i class="fas fa-check-circle text-emerald-400 mt-0.5"></i>
            <div>
                <span class="font-bold text-emerald-300">DO (Tugas Wajib Anda):</span>
                <p class="text-slate-300 text-[11px] mt-0.5">
                    @if($userRole === 'admin')
                        Audit aktivitas mutasi berkas per subbag &amp; pastikan hak akses personel sesuai penugasan.
                    @elseif($userRole === 'kabag')
                        Gunakan TTD Digital Harian 1-klik untuk efisiensi paraf surat keluar &amp; cek antrean darurat SLA.
                    @elseif($userRole === 'kasubbag')
                        Beri disposisi &amp; tunjuk PIC anggota pelaksana; jika surat info umum cukup tandai Arsip.
                    @elseif($userRole === 'anggota' && $userSubbag === 'urmin')
                        Input surma baru segera setelah diterima, tentukan subbag tujuan, dan kelola nomor TAUD serta arsip berkas clear.
                    @elseif($userRole === 'anggota')
                        Pelajari disposisi kasubbag, buat konsep surat keluar dengan catatan peruntukan instansi tujuan, dan bubuhkan paraf konseptor.
                    @endif
                </p>
            </div>
        </div>

        <div class="bg-red-500/5 border border-red-500/20 rounded-lg p-2.5 flex items-start space-x-2">
            <i class="fas fa-times-circle text-red-400 mt-0.5"></i>
            <div>
                <span class="font-bold text-red-300">DON'T (Larangan Keras):</span>
                <p class="text-slate-300 text-[11px] mt-0.5">
                    @if($userRole === 'admin')
                        Jangan mengubah subbag akun aktif tanpa koordinasi karena berpengaruh pada filter arsip.
                    @elseif($userRole === 'kabag')
                        Jangan membiarkan antrean surat mandek >3 hari tanpa tindak lanjut disposisi pimpinan.
                    @elseif($userRole === 'kasubbag')
                        Jangan meneruskan konsep surat ke meja Kabag tanpa verifikasi dan paraf Kasubbag terlebih dahulu.
                    @elseif($userRole === 'anggota' && $userSubbag === 'urmin')
                        Jangan mendistribusikan surat masuk tanpa memilih subbag tujuan yang valid.
                    @elseif($userRole === 'anggota')
                        Jangan mengirim surat keluar langsung ke instansi eksternal/TAUD tanpa melalui verifikasi Kasubbag &amp; Urmin.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endif
