<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SuratMasuk;
use App\Models\SuratMasukSubbag;
use App\Models\SuratKeluar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks for clean truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        SuratMasuk::truncate();
        SuratMasukSubbag::truncate();
        SuratKeluar::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // =============================================================
        // 1. SEED USERS (Password semuanya: password123)
        // =============================================================

        // ADMIN
        User::create([
            'username'     => 'admin',
            'password'     => Hash::make('password123'),
            'nama_lengkap' => 'Admin Sistem InterOps',
            'role'         => 'admin',
            'subbag'       => null
        ]);

        // KABAG
        User::create([
            'username'     => 'kabag',
            'password'     => Hash::make('password123'),
            'nama_lengkap' => 'Kombes Pol Kepala Bagian (KABAG)',
            'role'         => 'kabag',
            'subbag'       => null
        ]);

        // KASUBBAG (5 subbag)
        $kasubbagsData = [
            ['username' => 'kasubbag_urmin', 'nama_lengkap' => 'Kasubbag Urusan Administrasi',   'subbag' => 'urmin'],
            ['username' => 'kasubbag_bhi',   'nama_lengkap' => 'Kasubbag Hukum Internasional',   'subbag' => 'bhi'],
            ['username' => 'kasubbag_bi',    'nama_lengkap' => 'Kasubbag Bankuminter',           'subbag' => 'bi'],
            ['username' => 'kasubbag_ops',   'nama_lengkap' => 'Kasubbag Operasional Interpol',  'subbag' => 'ops'],
            ['username' => 'kasubbag_koor',  'nama_lengkap' => 'Kasubbag Koordinasi Interpol',   'subbag' => 'koor'],
        ];
        foreach ($kasubbagsData as $data) {
            User::create(array_merge($data, ['password' => Hash::make('password123'), 'role' => 'kasubbag']));
        }

        // ANGGOTA (5 subbag)
        $anggotaData = [
            ['username' => 'anggota_urmin', 'nama_lengkap' => 'Bripda Staf Urmin',   'subbag' => 'urmin'],
            ['username' => 'anggota_bhi',   'nama_lengkap' => 'Bripda Staf BHI',     'subbag' => 'bhi'],
            ['username' => 'anggota_bi',    'nama_lengkap' => 'Bripda Staf BI',      'subbag' => 'bi'],
            ['username' => 'anggota_ops',   'nama_lengkap' => 'Bripda Staf Ops',     'subbag' => 'ops'],
            ['username' => 'anggota_koor',  'nama_lengkap' => 'Bripda Staf Koor',    'subbag' => 'koor'],
        ];
        foreach ($anggotaData as $data) {
            User::create(array_merge($data, ['password' => Hash::make('password123'), 'role' => 'anggota']));
        }

        // =============================================================
        // 2. SEED SURAT MASUK (SURMA) PER SUBBAG
        // =============================================================
        
        $suratMasukList = [
            // --- SUBBAG OPS ---
            [
                'no_surat'          => 'OPS/INTERPOL/2026/089/TLJ',
                'dari'              => 'General Secretariat NCB Lyon (IPSG)',
                'kepada'            => 'Kadivhubinter Polri u.p. Kasubbag Ops',
                'perihal'           => 'Pemberitahuan Pelaksanaan Operasi Bersama Penindakan Cyber Fraud "Operation First Light 2026"',
                'tanggal_masuk'     => Carbon::now()->subDays(1)->format('Y-m-d'),
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['ops']
            ],
            [
                'no_surat'          => 'B/342/VIII/RES.1.24/2026/Bareskrim',
                'dari'              => 'Bareskrim Polri - Dittipidnarkoba',
                'kepada'            => 'Kadivhubinter Polri',
                'perihal'           => 'Permohonan Penerbitan Red Notice DPO Kasus Narkotika Sindikat Golden Triangle',
                'tanggal_masuk'     => Carbon::now()->subDays(5)->format('Y-m-d'), // SLA Breach
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['ops']
            ],
            [
                'no_surat'          => 'B-812/LN.02/07/2026/NCB-SG',
                'dari'              => 'NCB Singapore (Singapore Police Force)',
                'kepada'            => 'Ses NCB Interpol Indonesia',
                'perihal'           => 'Hasil Koordinasi Operasi Penangkapan Pelaku Transnational Crime di Changi Airport',
                'tanggal_masuk'     => Carbon::now()->subDays(10)->format('Y-m-d'),
                'status'            => 'disposisi',
                'no_dispo'          => 'DSP/2026/VIII/4102',
                'disposisi_kabag'   => 'Tindak Lanjuti (TLJ) - Siapkan Konsep Balasan apresiasi dan koordinasikan tindak lanjut penyidikan.',
                'disposisi_kasubag' => 'PIC: Bripda Staf Ops. Segera susun draf surat konfirmasi.',
                'subbags'           => ['ops']
            ],

            // --- SUBBAG BHI (Bantuan Hukum Internasional) ---
            [
                'no_surat'          => 'B-1209/E/Ejp/08/2026/Kejagung',
                'dari'              => 'Jaksa Agung Muda Bidang Tindak Pidana Khusus Kejaksaan Agung RI',
                'kepada'            => 'Kadivhubinter Polri u.p. Kasubbag BHI',
                'perihal'           => 'Permohonan Mutual Legal Assistance (MLA) dalam Perkara Dugaan Tipikor Transnasional',
                'tanggal_masuk'     => Carbon::now()->subDays(2)->format('Y-m-d'),
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['bhi']
            ],
            [
                'no_surat'          => 'D/KEMLU/DHI/2026/554',
                'dari'              => 'Direktorat Hukum dan Perjanjian Internasional Kemenlu RI',
                'kepada'            => 'Kadivhubinter Polri',
                'perihal'           => 'Penyampaian Permintaan Ekstradisi Tersangka WNA dari Pemerintah Federal Australia',
                'tanggal_masuk'     => Carbon::now()->subDays(6)->format('Y-m-d'), // SLA Breach
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['bhi']
            ],
            [
                'no_surat'          => 'B/440/VII/HUK.2.1/2026/Divkum',
                'dari'              => 'Divisi Hukum Polri',
                'kepada'            => 'Kadivhubinter Polri',
                'perihal'           => 'Telaahan Legalitas Draft Perjanjian Transfer of Sentenced Persons (TSP)',
                'tanggal_masuk'     => Carbon::now()->subDays(12)->format('Y-m-d'),
                'status'            => 'disposisi',
                'no_dispo'          => 'DSP/2026/VII/3211',
                'disposisi_kabag'   => 'Datakan & Pelajari untuk bahan masukan rapat koordinasi antar kementerian.',
                'disposisi_kasubag' => 'PIC: Bripda Staf BHI. Siapkan telaahan yuridis komparatif.',
                'subbags'           => ['bhi']
            ],

            // --- SUBBAG KOORDINASI (KOOR) ---
            [
                'no_surat'          => 'UND-192/POLHUKAM/08/2026',
                'dari'              => 'Kemenko Bidang Kemaritiman dan Komando Keamanan RI',
                'kepada'            => 'Kadivhubinter Polri u.p. Kasubbag Koor',
                'perihal'           => 'Undangan Rapat Koordinasi Tingkat Tinggi Pengamanan KTT Internasional ASEAN 2026',
                'tanggal_masuk'     => Carbon::now()->subDays(1)->format('Y-m-d'),
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['koor']
            ],
            [
                'no_surat'          => 'SEC/IPSG/CONF/94/2026',
                'dari'              => 'Executive Committee Interpol General Secretariat Lyon',
                'kepada'            => 'Head of National Central Bureau (NCB) Jakarta',
                'perihal'           => 'Call for Papers and Country Delegations for the 94th Interpol General Assembly',
                'tanggal_masuk'     => Carbon::now()->subDays(4)->format('Y-m-d'), // SLA Breach
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['koor']
            ],
            [
                'no_surat'          => 'B/670/VII/HUM.1.1/2026/SetNCB',
                'dari'              => 'Sekretariat NCB Interpol Indonesia',
                'kepada'            => 'Para Kasubbag Divhubinter Polri',
                'perihal'           => 'Jadwal Agenda Rapat Pleno Koordinasi Tahunan Satuan Fungsi Kerjasama Luar Negeri',
                'tanggal_masuk'     => Carbon::now()->subDays(9)->format('Y-m-d'),
                'status'            => 'disposisi',
                'no_dispo'          => 'DSP/2026/VIII/1890',
                'disposisi_kabag'   => 'Hadiri / Laksanakan Kegiatan dan laporkan ringkasan butir kesepakatan.',
                'disposisi_kasubag' => 'PIC: Bripda Staf Koor. Siapkan materi paparan slide.',
                'subbags'           => ['koor']
            ],

            // --- SUBBAG BILATERAL (BI) ---
            [
                'no_surat'          => 'ND-088/POLRI-PDRM/BILAT/2026',
                'dari'              => 'Polis DiRaja Malaysia (PDRM) Liaison Office Jakarta',
                'kepada'            => 'Kadivhubinter Polri u.p. Kasubbag BI',
                'perihal'           => 'Nota Diplomatik Permohonan Pertemuan Bilateral Kerjasama Perbatasan Maritim Kalimantan',
                'tanggal_masuk'     => Carbon::now()->subDays(2)->format('Y-m-d'),
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['bi']
            ],
            [
                'no_surat'          => 'AFP/SLO/JAKARTA/2026/119',
                'dari'              => 'Australian Federal Police (AFP) Embassy Office',
                'kepada'            => 'Ses NCB Interpol Indonesia',
                'perihal'           => 'Pertukaran Informasi Terkait Dugaan Peredaran Gelap Narkotika Lintas Benua',
                'tanggal_masuk'     => Carbon::now()->subDays(7)->format('Y-m-d'), // SLA Breach
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['bi']
            ],
            [
                'no_surat'          => 'B/512/VII/KORP/2026/Bareskrim',
                'dari'              => 'Bareskrim Polri - Dit Tipidter',
                'kepada'            => 'Kadivhubinter Polri',
                'perihal'           => 'Laporan Hasil Penanganan Kasus Penyelundupan Satwa Dilindungi Antara RI - Filipina',
                'tanggal_masuk'     => Carbon::now()->subDays(15)->format('Y-m-d'),
                'status'            => 'disposisi',
                'no_dispo'          => 'DSP/2026/VII/2984',
                'disposisi_kabag'   => 'Simpan / Diarsipkan Saja (Info Rutin Tanpa Balasan)',
                'disposisi_kasubag' => 'Diarsipkan dalam database intelijen bilateral.',
                'subbags'           => ['bi']
            ],

            // --- SUBBAG URUSAN ADMINISTRASI (URMIN) ---
            [
                'no_surat'          => 'B/ND-211/VIII/LOG.3.1/2026/TAUD',
                'dari'              => 'Tata Usaha dan Urusan Dalam (TAUD) Divhubinter',
                'kepada'            => 'Kasubbag Urusan Administrasi (URMIN)',
                'perihal'           => 'Pengajuan Kebutuhan Pemeliharaan Server Kearsipan Digital dan Scanner Berkas Triwulan III',
                'tanggal_masuk'     => Carbon::now()->subDays(1)->format('Y-m-d'),
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['urmin']
            ],
            [
                'no_surat'          => 'SE/14/VIII/HUM.5.1/2026/SesNCB',
                'dari'              => 'Sekretaris NCB Interpol Indonesia',
                'kepada'            => 'Seluruh Personel Divhubinter Polri',
                'perihal'           => 'Surat Edaran: Kewajiban Penggunaan Sistem Kearsipan Terpadu InterOps-Hub',
                'tanggal_masuk'     => Carbon::now()->subDays(4)->format('Y-m-d'), // SLA Breach
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['urmin']
            ],
            [
                'no_surat'          => 'B/190/VII/KEU.2.4/2026/Puskeu',
                'dari'              => 'Pusat Keuangan Polri',
                'kepada'            => 'Kasubbag Urmin Divhubinter Polri',
                'perihal'           => 'Rekonsiliasi Realisasi Anggaran Perjalanan Dinas Luar Negeri Periode Semester I 2026',
                'tanggal_masuk'     => Carbon::now()->subDays(14)->format('Y-m-d'),
                'status'            => 'disposisi',
                'no_dispo'          => 'DSP/2026/VII/1120',
                'disposisi_kabag'   => 'Tindak Lanjuti dan selesaikan rekonsiliasi bersama bendahara pengeluaran.',
                'disposisi_kasubag' => 'PIC: Bripda Staf Urmin. Koordinasikan dengan verifikator Puskeu.',
                'subbags'           => ['urmin']
            ],

            // --- SURAT MASUK DUAL-SUBBAG (Distribusi ke 2 Subbag Sekaligus) ---
            [
                'no_surat'          => 'B-998/INTER-SEC/08/2026',
                'dari'              => 'Kementerian Koordinator Bidang Polhukam RI',
                'kepada'            => 'Kadivhubinter Polri',
                'perihal'           => 'Koordinasi Terpadu Penanganan Sindikat Human Trafficking (TPPO) dan Bantuan Hukum Internasional',
                'tanggal_masuk'     => Carbon::now()->subDays(2)->format('Y-m-d'),
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['ops', 'bhi']
            ],
            [
                'no_surat'          => 'D/ASEANAPOL/SEC/2026/71',
                'dari'              => 'Secretariat of ASEANAPOL Kuala Lumpur',
                'kepada'            => 'Head of NCB Interpol Indonesia',
                'perihal'           => 'Joint Maritime Border Patrol Protocol and Bilateral Focal Point Meeting',
                'tanggal_masuk'     => Carbon::now()->subDays(3)->format('Y-m-d'),
                'status'            => 'pending',
                'no_dispo'          => null,
                'disposisi_kabag'   => null,
                'disposisi_kasubag' => null,
                'subbags'           => ['koor', 'bi']
            ],
        ];

        foreach ($suratMasukList as $item) {
            $subbags = $item['subbags'];
            unset($item['subbags']);

            $surat = SuratMasuk::create($item);
            foreach ($subbags as $sb) {
                SuratMasukSubbag::create([
                    'surat_masuk_id' => $surat->id,
                    'subbag'         => $sb
                ]);
            }
        }

        // =============================================================
        // 3. SEED SURAT KELUAR (SURKEL) PER SUBBAG & MENUNGGU TTD KABAG
        // =============================================================

        $suratKeluarList = [
            // --- SUBBAG OPS ---
            [
                'no_surat'           => 'B/DRAF-01/IX/HUM.4.4.9./2026/OPS',
                'kepada'             => 'Kabareskrim Polri u.p. Direktur Tindak Pidana Siber',
                'tanggal_surat'      => Carbon::now()->toDateString(),
                'dari'               => 'Bripda Staf Ops (Konseptor Ops)',
                'tanggal_input'      => Carbon::now()->toDateString(),
                'perihal'            => 'Penyampaian Draf Rencana Operasi Penindakan Cyber Fraud Interpol Lionfish 2026',
                'subbag'             => 'ops',
                'keterangan_tujuan'  => 'Dikirim ke Dittipidsiber Bareskrim untuk koordinasi penindakan sindikat online scammer lintas negara.',
                'status_paraf_kabag' => 'pending', // Menunggu TTD Kabag
                'paraf_kabag_at'     => null,
                'catatan_kabag'      => null,
            ],
            [
                'no_surat'           => 'B/124/VIII/HUM.4.4.9./2026/OPS',
                'kepada'             => 'Direktur Pengawasan dan Penindakan Keimigrasian Kemenkumham RI',
                'tanggal_surat'      => Carbon::now()->subDays(8)->format('Y-m-d'),
                'dari'               => 'Kadivhubinter Polri',
                'tanggal_input'      => Carbon::now()->subDays(8)->format('Y-m-d'),
                'perihal'            => 'Permohonan Pencegahan Ke Luar Negeri dan Pencabutan Paspor DPO Kasus Narkotika',
                'subbag'             => 'ops',
                'keterangan_tujuan'  => 'Ditujukan ke Ditjen Imigrasi guna pencekalan tersangka Red Notice di seluruh TPI.',
                'status_paraf_kabag' => 'disetujui',
                'paraf_kabag_at'     => Carbon::now()->subDays(7),
                'catatan_kabag'      => 'Disetujui. Segera kirimkan tembusan ke Dittipidnarkoba Bareskrim.',
            ],
            [
                'no_surat'           => 'B/DRAF-04/VIII/HUM.4.4.9./2026/OPS',
                'kepada'             => 'Kepala Kantor Bea Cukai Bandara Soekarno Hatta',
                'tanggal_surat'      => Carbon::now()->subDays(12)->format('Y-m-d'),
                'dari'               => 'Bripda Staf Ops',
                'tanggal_input'      => Carbon::now()->subDays(12)->format('Y-m-d'),
                'perihal'            => 'Pengawasan Paket Kargo Jalur Internasional Terindikasi Barang Selundupan',
                'subbag'             => 'ops',
                'keterangan_tujuan'  => 'Koordinasi pengetatan x-ray kargo udara bandara internasional.',
                'status_paraf_kabag' => 'ditolak',
                'paraf_kabag_at'     => Carbon::now()->subDays(11),
                'catatan_kabag'      => 'Mohon lengkapi nomor airway bill dan identitas penerima paket sebelum diajukan kembali.',
            ],

            // --- SUBBAG BHI (Bantuan Hukum Internasional) ---
            [
                'no_surat'           => 'B/DRAF-02/IX/HUM.4.1./2026/BHI',
                'kepada'             => 'Direktur Jenderal Hukum dan Perjanjian Internasional Kemenlu RI',
                'tanggal_surat'      => Carbon::now()->toDateString(),
                'dari'               => 'Bripda Staf BHI (Konseptor BHI)',
                'tanggal_input'      => Carbon::now()->toDateString(),
                'perihal'            => 'Penyampaian Berkas Permohonan Ekstradisi Tersangka WNA Buronan Interpol Australia',
                'subbag'             => 'bhi',
                'keterangan_tujuan'  => 'Diteruskan ke Kemenlu untuk penyampaian nota diplomatik ekstradisi ke Kedubes Australia.',
                'status_paraf_kabag' => 'pending', // Menunggu TTD Kabag
                'paraf_kabag_at'     => null,
                'catatan_kabag'      => null,
            ],
            [
                'no_surat'           => 'B/210/VIII/HUM.4.1./2026/BHI',
                'kepada'             => 'Jaksa Agung Muda Bidang Tindak Pidana Khusus Kejaksaan Agung RI',
                'tanggal_surat'      => Carbon::now()->subDays(6)->format('Y-m-d'),
                'dari'               => 'Kadivhubinter Polri',
                'tanggal_input'      => Carbon::now()->subDays(6)->format('Y-m-d'),
                'perihal'            => 'Penyampaian Jawaban Permintaan Mutual Legal Assistance (MLA) dari Pemerintah Singapura',
                'subbag'             => 'bhi',
                'keterangan_tujuan'  => 'Penyerahan salinan putusan pengadilan dan rekening koran hasil tracing aset tipikor.',
                'status_paraf_kabag' => 'disetujui',
                'paraf_kabag_at'     => Carbon::now()->subDays(5),
                'catatan_kabag'      => 'Disetujui. Kirimkan berkas fisik melalui kurir resmi TAUD.',
            ],

            // --- SUBBAG KOORDINASI (KOOR) ---
            [
                'no_surat'           => 'B/DRAF-03/IX/HUM.4.4.9./2026/KOOR',
                'kepada'             => 'Sekretaris Kementerian Koordinator Bidang Polhukam RI',
                'tanggal_surat'      => Carbon::now()->toDateString(),
                'dari'               => 'Bripda Staf Koor (Konseptor Koor)',
                'tanggal_input'      => Carbon::now()->toDateString(),
                'perihal'            => 'Konfirmasi Kehadiran dan Susunan Delegasi Polri pada Rapat Pleno Keamanan KTT ASEAN',
                'subbag'             => 'koor',
                'keterangan_tujuan'  => 'Ditujukan ke Kemenko Polhukam untuk pendaftaran ID pass delegasi dan protokoler.',
                'status_paraf_kabag' => 'pending', // Menunggu TTD Kabag
                'paraf_kabag_at'     => null,
                'catatan_kabag'      => null,
            ],
            [
                'no_surat'           => 'B/188/VIII/HUM.4.4.9./2026/KOOR',
                'kepada'             => 'Kepala Divisi Hubungan Masyarakat Polri',
                'tanggal_surat'      => Carbon::now()->subDays(10)->format('Y-m-d'),
                'dari'               => 'Kadivhubinter Polri',
                'tanggal_input'      => Carbon::now()->subDays(10)->format('Y-m-d'),
                'perihal'            => 'Penyampaian Press Release Keberhasilan Penangkapan Buronan Transnasional',
                'subbag'             => 'koor',
                'keterangan_tujuan'  => 'Bahan konferensi pers bersama Kadivhumas dan Kabareskrim.',
                'status_paraf_kabag' => 'disetujui',
                'paraf_kabag_at'     => Carbon::now()->subDays(9),
                'catatan_kabag'      => 'Disetujui. Pastikan foto buronan disamarkan sesuai kode etik jurnalistik.',
            ],

            // --- SUBBAG BILATERAL (BI) ---
            [
                'no_surat'           => 'B/DRAF-05/IX/HUM.4.2./2026/BI',
                'kepada'             => 'Senior Liaison Officer Australian Federal Police (AFP) Embassy Jakarta',
                'tanggal_surat'      => Carbon::now()->toDateString(),
                'dari'               => 'Bripda Staf BI (Konseptor BI)',
                'tanggal_input'      => Carbon::now()->toDateString(),
                'perihal'            => 'Draft Agenda and List of Participants for Bilateral Police Joint Consultation 2026',
                'subbag'             => 'bi',
                'keterangan_tujuan'  => 'Dikirim via surat dinas ke perwakilan kepolisian Australia guna finalisasi jadwal pertemuan.',
                'status_paraf_kabag' => 'pending', // Menunggu TTD Kabag
                'paraf_kabag_at'     => null,
                'catatan_kabag'      => null,
            ],
            [
                'no_surat'           => 'B/95/VIII/HUM.4.2./2026/BI',
                'kepada'             => 'Ketua Polis Negara Polis DiRaja Malaysia (PDRM) Bukit Aman',
                'tanggal_surat'      => Carbon::now()->subDays(5)->format('Y-m-d'),
                'dari'               => 'Kadivhubinter Polri',
                'tanggal_input'      => Carbon::now()->subDays(5)->format('Y-m-d'),
                'perihal'            => 'Ucapan Selamat Hari Polis Malaysia dan Penguatan Kerjasama Pertukaran Personel',
                'subbag'             => 'bi',
                'keterangan_tujuan'  => 'Diplomasi kepolisian dan penguatan hubungan persahabatan antar pimpinan kepolisian serumpun.',
                'status_paraf_kabag' => 'disetujui',
                'paraf_kabag_at'     => Carbon::now()->subDays(4),
                'catatan_kabag'      => 'Disetujui. Kirimkan cinderamata plakat Divhubinter bersama surat.',
            ],

            // --- SUBBAG URUSAN ADMINISTRASI (URMIN) ---
            [
                'no_surat'           => 'B/DRAF-06/IX/HUM.4.4.9./2026/URMIN',
                'kepada'             => 'Kepala Tata Usaha dan Urusan Dalam (TAUD) Divhubinter Polri',
                'tanggal_surat'      => Carbon::now()->toDateString(),
                'dari'               => 'Bripda Staf Urmin (Konseptor Urmin)',
                'tanggal_input'      => Carbon::now()->toDateString(),
                'perihal'            => 'Permohonan Nomor Surat Dinas Resmi dan Distribusi Arsip Eksternal Batch-IX',
                'subbag'             => 'urmin',
                'keterangan_tujuan'  => 'Pengajuan penerbitan nomor surat dinas resmi TAUD untuk berkas yang telah diparaf Kabag.',
                'status_paraf_kabag' => 'pending', // Menunggu TTD Kabag
                'paraf_kabag_at'     => null,
                'catatan_kabag'      => null,
            ],
            [
                'no_surat'           => 'B/311/VIII/HUM.4.4.9./2026/URMIN',
                'kepada'             => 'Kepala Biro Pengkajian dan Strategi Slog Polri',
                'tanggal_surat'      => Carbon::now()->subDays(15)->format('Y-m-d'),
                'dari'               => 'Kadivhubinter Polri',
                'tanggal_input'      => Carbon::now()->subDays(15)->format('Y-m-d'),
                'perihal'            => 'Laporan Rekapitulasi Inventaris Arsip Berkas Rahasia dan Perlengkapan Ruang Server',
                'subbag'             => 'urmin',
                'keterangan_tujuan'  => 'Pelaporan berkala aset kearsipan fisik dan infrastruktur digital Divhubinter.',
                'status_paraf_kabag' => 'disetujui',
                'paraf_kabag_at'     => Carbon::now()->subDays(14),
                'catatan_kabag'      => 'Disetujui.',
            ],
        ];

        foreach ($suratKeluarList as $keluar) {
            SuratKeluar::create($keluar);
        }

        $this->command->info('Database berhasil di-seed ulang dengan dummy data realistis untuk seluruh subbag (OPS, BHI, KOOR, BI, URMIN) & antrean TTD Kabag!');
    }
}