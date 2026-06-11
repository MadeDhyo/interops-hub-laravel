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
        $admin = User::create([
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Made Admin Hub',
            'role' => 'admin',
        ]);

        $operator = User::create([
            'username' => 'operator',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Siti Operator',
            'role' => 'operator',
        ]);

        $pimpinan = User::create([
            'username' => 'pimpinan',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Bapak Kepala Hub',
            'role' => 'pimpinan',
        ]);

        $staf = User::create([
            'username' => 'staf',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Gede Staf',
            'role' => 'staf',
        ]);

        // 2. SEED DATA SURAT MASUK
        SuratMasuk::create([
            'kepada' => 'Kepala Kantor',
            'dari' => 'Kementerian Digital',
            'perihal' => 'Undangan Rapat Koordinasi Interoperabilitas Sistem',
            'tanggal_masuk' => Carbon::now()->format('Y-m-d'),
            'no_surat' => 'UND-001/KOMINFO/2026',
            'status' => 'pending',
        ]);

        SuratMasuk::create([
            'kepada' => 'Bagian Kepegawaian',
            'dari' => 'Badan Kepegawaian Negara',
            'perihal' => 'Pembaruan Data Sistem Informasi Aparatur',
            'tanggal_masuk' => Carbon::now()->subDays(4)->format('Y-m-d'), // Udah lewat 3 hari (bakal masuk hitungan SLA)
            'no_surat' => 'B/210/BKN/V/2026',
            'status' => 'pending',
        ]);

        // 3. SEED DATA SURAT KELUAR
        SuratKeluar::create([
            'kepada' => 'Dinas Kominfo Provinsi Bali',
            'no_surat' => 'OUT-2026-001',
            'tanggal_surat' => Carbon::now()->format('Y-m-d'),
            'dari' => 'InterOps Hub Center',
            'tanggal_input' => Carbon::now()->format('Y-m-d'),
            'perihal' => 'Pengiriman Log Integrasi Data Triwulan I',
        ]);

        $this->command->info('Database berhasil di-seed! Akun ready digunakan.');
    }
}