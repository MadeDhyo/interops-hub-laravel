# InterOps-Hub

**Sistem Tata Kelola Kearsipan, Disposisi Berjenjang & Digitalisasi Surat Dinas**  
*Bagian Kerjasama Kawasan & Kejahatan Internasional — Divhubinter Polri / Interpol NCB Jakarta*

---

## 📌 Tentang Sistem

**InterOps-Hub** adalah platform kearsipan digital terpadu yang dirancang khusus untuk operasional persuratan dinas di lingkungan Divisi Hubungan Internasional Polri. Sistem ini mengintegrasikan alur **Surat Masuk (Surma)** dan **Surat Keluar (Surkel)** dari tingkat pimpinan hingga staf pelaksana, dilengkapi pemindaian AI otomatis (OCR), tanda tangan/paraf digital harian, tracking SLA disposisi, serta isolasi akses berbasis peran dan subbag.

---

## 🚀 Fitur Unggulan

### 1. Manajemen Surat Masuk (Surma)
- **Scan AI Otomatis (Gemini OCR):** Ekstraksi otomatis nomor surat, pengirim, penerima (KADIVHUBINTER / NCB JAKARTA), dan perihal dokumen PDF dalam hitungan detik menggunakan Google Gemini API.
- **Distribusi Multi-Subbag:** Satu surat masuk dapat didistribusikan ke 1 hingga 2 Subbag sekaligus (OPS, BHI, BI, KOOR, URMIN).
- **Disposisi Berjenjang:** Format nomor disposisi otomatis `DSP/{TAHUN}/{ROMAWI_BULAN}/{UNIQID}` dengan penugasan instruksi Kabag dan penunjukan Person in Charge (PIC) oleh Kasubbag.
- **Tindak Lanjut Anggota:** Form khusus staf pelaksana/PIC untuk memperbarui status tindak lanjut, arsip berkas, atau pembuatan draf surat balasan.
- **SLA Breach Monitoring:** Indikator visual real-time untuk surat pending yang melebihi batas waktu 3 hari kalender.
- **Export Data:** Rekapitulasi data surat masuk ke format CSV (UTF-8 BOM compatible).

### 2. Manajemen Surat Keluar (Surkel)
- **Draf Persuratan per Subbag:** Anggota staf menginput draf surat keluar dengan pelabelan subbag otomatis.
- **Paraf Digital Kabag:** Alur persetujuan (*acc*) atau penolakan revisi oleh Kepala Bagian.
- **Reusable Daily Signature:** Kabag cukup mengunggah/menggores tanda tangan digital sekali per hari; sistem otomatis menggunakan kembali tanda tangan aktif untuk dokumen berikutnya di hari yang sama.
- **Pencarian Teks Penuh (Full-Text Search):** Background job mengekstrak isi teks PDF surat keluar untuk mendukung pencarian kata kunci yang mendalam.

### 3. Keamanan & Role-Based Access Control (RBAC)
- **Isolasi Subbag:** Kasubbag dan Anggota hanya dapat melihat dan memproses surat milik subbag mereka sendiri (kecuali Urmin, Kabag, dan Admin yang memiliki visibilitas lintas subbag).
- **Aturan Input Ketat:** Kasubbag Urmin tidak diperkenankan menginput surat masuk; hak input khusus untuk Anggota Staf Urmin dan Admin.
- **Kunci Sesi Cepat (Session Lock):** Fitur penguncian layar instan (`/locked`) dengan proteksi kata sandi tanpa memutus sesi login aktif.
- **Secure Document Serving:** Berkas PDF disimpan di penyimpanan privat lokal (`storage/app/arsip_pdf/`) dan hanya dapat diunduh melalui verifikasi otorisasi controller.
- **Activity Logs:** Pencatatan otomatis setiap aksi penting (login, input surat, penerbitan disposisi, tindak lanjut, paraf dokumen).

---

## 🏛️ Matriks Hak Akses & Subbag

Sistem mencakup 5 Sub-Bagian utama:
1. **OPS** — Operasional Interpol
2. **BHI** — Hukum Internasional & Kerjasama Hukum
3. **BI** — Bilateral & Diplomasi Kepolisian
4. **KOOR** — Koordinasi Hubungan Antar Lembaga
5. **URMIN** — Urusan Administrasi & Tata Usaha

| Role | Input Surma | Disposisi Surma | Tindak Lanjut Surma | Input Surkel | Paraf Surkel | Visibilitas Data |
|---|:---:|:---:|:---:|:---:|:---:|---|
| **Admin** | ✅ | ✅ | 👁️ (Readonly) | ✅ | ✅ | Semua Subbag |
| **Kabag** | ❌ | ✅ | 👁️ (Readonly) | ❌ | ✅ | Semua Subbag (Surkel & Dashboard) |
| **Kasubbag Urmin** | ❌ | ✅ | 👁️ (Readonly) | ✅ | ❌ | Semua Subbag |
| **Kasubbag Non-Urmin** | ❌ | ✅ | 👁️ (Readonly) | ✅ | ❌ | Hanya Subbag Sendiri |
| **Anggota Urmin** | ✅ | ❌ | ✅ | ✅ | ❌ | Semua Subbag |
| **Anggota Non-Urmin** | ❌ | ❌ | ✅ | ✅ | ❌ | Hanya Subbag Sendiri |

---

## 🛠️ Tech Stack

- **Backend:** Laravel 12.x (PHP 8.2+)
- **Frontend:** Blade Views, jQuery, Tailwind CSS / Modern CSS, FontAwesome Icons
- **Database:** MySQL / MariaDB
- **PDF & AI Engine:** `smalot/pdfparser`, Google Gemini 3.5 Flash API
- **Queue & Async:** Laravel Database Queue (`ProcessOcrSuratMasuk`, `ExtractPdfTextSuratKeluar`)

---

## ⚙️ Persyaratan Sistem

- PHP `>= 8.2` (dengan ekstensi: `pdo_mysql`, `curl`, `mbstring`, `fileinfo`, `openssl`)
- Composer `>= 2.x`
- MySQL `>= 8.0` atau MariaDB `>= 10.4`
- Node.js & NPM (opsional jika ingin memodifikasi aset frontend)
- Google Gemini API Key (untuk fitur Scan AI OCR)

---

## 📦 Panduan Instalasi

### 1. Clone Repositori
```bash
git clone https://github.com/username/interops-hub-laravel.git
cd interops-hub-laravel
```

### 2. Instal Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` ke `.env`:
```bash
copy .env.example .env
```
Sesuaikan konfigurasi database dan API key pada `.env`:
```env
APP_NAME=InterOps-Hub
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:5050

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=interops_hub
DB_USERNAME=root
DB_PASSWORD=

# Kunci API untuk Scan AI OCR Dokumen
GEMINI_API_KEY=your_google_gemini_api_key_here
```

### 4. Generate App Key & Jalankan Migrasi + Seeder
```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

### 5. Jalankan Background Queue Worker
Fitur ekstraksi teks PDF otomatis membutuhkan antrean queue aktif:
```bash
php artisan queue:work
```

### 6. Jalankan Server Aplikasi
```bash
php artisan serve --port=5050
```
Buka browser di: `http://localhost:5050`

---

## 🔑 Akun Demo Pengembangan

Semua akun pengujian di bawah ini menggunakan kata sandi default: **`password123`**

| Username | Role | Subbag | Deskripsi Akses |
|---|---|---|---|
| `admin` | Admin | - | Akses penuh & manajemen user |
| `kabag` | Kabag | - | Disposisi, paraf harian surat keluar |
| `kasubbag_urmin` | Kasubbag | Urmin | Disposisi, pantau seluruh surat masuk |
| `kasubbag_ops` | Kasubbag | OPS | Disposisi & tunjuk PIC surat masuk Subbag OPS |
| `kasubbag_bhi` | Kasubbag | BHI | Disposisi & tunjuk PIC surat masuk Subbag BHI |
| `kasubbag_koor` | Kasubbag | KOOR | Disposisi & tunjuk PIC surat masuk Subbag KOOR |
| `kasubbag_bi` | Kasubbag | BI | Disposisi & tunjuk PIC surat masuk Subbag BI |
| `anggota_urmin` | Anggota | Urmin | **Input surat masuk**, scan AI OCR |
| `anggota_ops` | Anggota | OPS | Input surat keluar & tindak lanjut Subbag OPS |
| `anggota_bhi` | Anggota | BHI | Input surat keluar & tindak lanjut Subbag BHI |
| `anggota_koor` | Anggota | KOOR | Input surat keluar & tindak lanjut Subbag KOOR |
| `anggota_bi` | Anggota | BI | Input surat keluar & tindak lanjut Subbag BI |

---

## 🔒 Dokumen Internal & Audit Keamanan

- **SOP & Regression Checklist:** Lihat file internal [`SYSTEM_DOCS.md`](SYSTEM_DOCS.md) untuk detail arsitektur, catatan bug fix, dan manual regression checklist.
- **Agent Directives & Skills:** Konfigurasi skill agent developer tersedia pada direktori `.agents/`.

---

## 📄 Lisensi

Proyek ini dikembangkan secara internal untuk kebutuhan dinas kearsipan dan administrasi kepolisian Divhubinter Polri.
