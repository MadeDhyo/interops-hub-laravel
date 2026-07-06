<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SEED DATA USERS (Password semuanya: password123)
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Made Admin & Operator',
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'pimpinan',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Bapak Kasubbag Jatranin Divhubinter',
            'role' => 'pimpinan',
        ]);

        User::create([
            'username' => 'anggota',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Brigadir Staf DivHubinter',
            'role' => 'staf',
        ]);

        // Pool Data Kloningan Variasi untuk Surat Masuk (Khas Hubinter & Intelkam)
        $dariSuratMasuk = [
            'Kemenko Polhukam RI', 'NCB Paris', 'NCB New-Delhi', 'NCB Manila', 'NCB Singapore', 
            'NCB Phnom-Penh', 'UK National Crime Agency (NCA)', 'OJK Regional Center', 
            'Dirjen Protokol Kemenlu RI', 'Bareskrim Polri', 'Badan Siber dan Sandi Negara (BSSN)'
        ];

        $kepadaSuratMasuk = [
            'Kadivhubinter Polri', 'Ses NCB Interpol Indonesia', 'Kabagkominter', 
            'Kabagjatranin', 'Kepala Biro Misi Internasional'
        ];

        $perihalSuratMasuk = [
            'Request for Case Coordination Meeting regarding Online Scam and Cyber Fraud Assets Tracking',
            'Invitation to Virtual Pre-Operational Briefing for Joint Transnational Operation Lionfish-Mayag V',
            'Request for Bilateral Meeting Schedule during the Annual Heads of NCB Conference in Lyon France',
            'Circular Letter: Questionnaire on Crypto Assets and Decentralized Infrastructure Used by Extremists',
            'Request Technical Assistance for Regional Workshop on Environmental Crime and Pangolin Scales Trafficking',
            'Notification of Interagency High-Level Coordination Meeting on Maritime Border Security Framework',
            'Submission of Final Evaluation Report for Counter-Terrorism Investigative Capability Programme (IPCP)',
            'Kind Reminder: Monitoring Progress in ICSE Database Post-Training and Trans transnational Operation Plan',
            'Invitation to Join Operational Phase of Anti-Illicit Pharmaceutical Products Spanning Until Late Year',
            'Re: Save The Date and Participant Nomination for Focus Group on Women in Policing Conference'
        ];

        $statusSuratMasuk = ['pending', 'disposisi'];
        $romawiBulan = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        // 2. GENERATE 47 DATA SURAT MASUK (Range Februari-Juni 2026 agar masuk filter SLA)
        for ($i = 1; $i <= 47; $i++) {
            $bulan = rand(2, 6); // Mundur dari Bulan 2 (Februari) sampai Bulan 6 (Juni)
            
            // Jaga-jaga agar data di bulan Juni tidak melompati tanggal hari ini (23 Juni 2026)
            if ($bulan === 6) {
                $hari = rand(1, 20); 
            } else {
                $hari = rand(1, 28);
            }

            $tanggalMasuk = Carbon::create(2026, $bulan, $hari)->format('Y-m-d');
            $status = $statusSuratMasuk[array_rand($statusSuratMasuk)];
            $romawi = $romawiBulan[$bulan - 1];

            $pola = rand(1, 3);
            if ($pola === 1) {
                $noSurat = "2026/" . rand(100, 499) . "/OEC/CNET/DRUGS-CASE/" . array_rand(['JSR', 'TLJ', 'TTR']) . "-TLJ";
            } elseif ($pola === 2) {
                $noSurat = "B-" . rand(700, 999) . "/LN.00.03/" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "/2026";
            } else {
                $noSurat = "CT/OPS/CT-TECH+/TR" . rand(1, 4) . "/OB-" . array_rand(['TLJ', 'DATAKAN']);
            }

            SuratMasuk::create([
                'no_surat' => $noSurat,
                'dari' => $dariSuratMasuk[array_rand($dariSuratMasuk)],
                'kepada' => $kepadaSuratMasuk[array_rand($kepadaSuratMasuk)],
                'perihal' => $perihalSuratMasuk[array_rand($perihalSuratMasuk)] . " (Reference Batch #" . rand(10, 99) . ")",
                'tanggal_masuk' => $tanggalMasuk,
                'status' => $status,
                'no_dispo' => $status === 'disposisi' ? 'DSP/2026/' . $romawi . '/' . rand(1000, 9999) : null,
                'disposisi_kabag' => $status === 'disposisi' ? 'Harap disiapkan bahan masukan serta koordinasikan dengan fungsi terkait.' : null,
                'disposisi_kasubag' => $status === 'disposisi' ? 'Laksanakan instruksi pimpinan dan siapkan administrasi perjalanan dinas.' : null,
                'file_pdf' => null
            ]);
        }

        // Pool Data Kloningan Variasi untuk Surat Keluar
        $dariSuratKeluar = [
            'Divhubinter Polri', 'NCB Jakarta', 'Bagjatranin', 'Bagkominter', 'Set NCB Interpol Indonesia'
        ];

        $kepadaSuratKeluar = [
            'Dirjen Kerja Sama Multilateral Kemenlu RI', 'Bareskrim Polri', 'Kabareskrim', 'Kepala BNN RI',
            'Dinas Kominfo Provinsi Bali', 'Fakultas Hukum Universitas Indonesia', 'Lembaga Sandi Negara',
            'Pusat Data dan Teknologi Informasi', 'Para Pejabat Daftar Terlampir'
        ];

        $perihalSuratKeluar = [
            'Permohonan Pengurusan Exit Permit dan Administrasi Perjalanan Dinas Luar Negeri Delegasi RI',
            'Pemberitahuan Informasi Pelatihan Internasional Interpol ICSE Database dan Cybercrime Investigation',
            'Permohonan Pengiriman Dokumen Konfirmasi Kepesertaan Workshop Enforcement Against Environmental Crimes',
            'Pelimpahan Surat Permohonan Ekstradisi dan Bantuan Hukum Timbal Balik (Mutual Legal Assistance)',
            'Pemberitahuan Informasi Awal Pelaksanaan Operasi Bersama Pangea XIX Global Phase',
            'Permohonan Bantuan Publikasi dan Distribusi Bahan Webinar Cyberbullying Awareness Form',
            'Penyampaian Laporan Hasil Pelaksanaan Menghadiri United Kingdom National Crime Agency Capacity Building',
            'Permohonan Bantuan Pengiriman Data Profiling Pelaku Penyelundupan Komoditi Dilindungi melalui Jalur I-24/7',
            'Pemberitahuan Informasi Iuran Wajib Kontribusi Tahunan Internasional Kepada Lembaga Dunia',
            'Permohonan Penugasan Personel sebagai Trainer pada Pelatihan Pelaporan Kasus Exploitation Against Children'
        ];

        // 3. GENERATE 23 DATA SURAT KELUAR (Range Februari-Juni 2026)
        for ($j = 1; $j <= 23; $j++) {
            $bulanKeluar = rand(2, 6);
            if ($bulanKeluar === 6) {
                $hariKeluar = rand(1, 20);
            } else {
                $hariKeluar = rand(1, 28);
            }
            $tanggalSurat = Carbon::create(2026, $bulanKeluar, $hariKeluar)->format('Y-m-d');
            $romawiKeluar = $romawiBulan[$bulanKeluar - 1];
            $subBagian = array_rand(['DIVHUBINTER', 'BAGJATRANIN', 'BAGKOMINTER']);

            if (rand(1, 2) === 1) {
                $noSuratKeluar = "B/" . rand(50, 450) . "/" . $romawiKeluar . "/HUM.4.4.9./2026/" . $subBagian;
            } else {
                $noSuratKeluar = "B/ND-" . rand(10, 99) . "/" . $romawiKeluar . "/HUM.4.1./2026/" . $subBagian;
            }

            SuratKeluar::create([
                'no_surat' => $noSuratKeluar,
                'kepada' => $kepadaSuratKeluar[array_rand($kepadaSuratKeluar)],
                'tanggal_surat' => $tanggalSurat,
                'dari' => $dariSuratKeluar[array_rand($dariSuratKeluar)],
                'tanggal_input' => $tanggalSurat,
                'perihal' => $perihalSuratKeluar[array_rand($perihalSuratKeluar)] . " Vol-" . $j,
                'file_pdf' => null
            ]);
        }

        $this->command->info('Database sukses di-seed! 47 Surat Masuk & 23 Surat Keluar format tanggal real-past siap digas.');
    }
}