# TASK INSTRUCTIONS: Tahap Integrasi Backend & Pengembangan (Laravel 12)

**Proyek:** Sistem Absensi UKM Taekwondo Kampus
**Status Saat Ini:** Fase Desain UI/UX (Draf HTML statis) telah selesai. Spesifikasi Kebutuhan (PRD) telah disetujui.
**Tujuan Dokumen:** Panduan langkah demi langkah bagi tim pengembang (Backend/Fullstack Engineer) untuk mengintegrasikan draf HTML ke dalam *framework* Laravel 12 dan mengimplementasikan logika bisnis sesuai PRD.

---

## FASE 1: Persiapan Lingkungan & Arsitektur Dasar

- [ ] **Inisialisasi Proyek Laravel 12:** Buat proyek Laravel baru.
- [ ] **Konfigurasi Database:** Atur koneksi database (MySQL/MariaDB) di file `.env`.
- [ ] **Instalasi Paket Autentikasi:** Instal dan konfigurasi **Laravel Breeze** (Blade stack) sebagai fondasi autentikasi.
- [ ] **Integrasi Aset Frontend (Vite & TailwindCSS):** 
  - Konfigurasi `tailwind.config.js` dengan menyalin pengaturan dari tag `<script id="tailwind-config">` yang ada pada draf HTML (termasuk warna kustom `brand`, `status`, font keluarga `Poppins` & `Inter`, serta konfigurasi `darkMode: 'class'`).
  - Pindahkan file CSS kustom dari tag `<style>` di HTML ke file CSS utama Vite (mis. `resources/css/app.css`).
- [ ] **Instalasi Paket Tambahan:**
  - `simplesoftwareio/simple-qrcode` (atau library QR code sejenis) untuk *generate* QR Code anggota.
  - `maatwebsite/excel` untuk fitur ekspor ke Excel (Rekap Kehadiran & Database Anggota).
  - `dompdf/dompdf` untuk konversi/cetak dokumen PDF (seperti kartu ID, jika diperlukan di backend).
  - **Laravel Livewire** (v3) untuk menangani interaktivitas *real-time* tanpa *full-page reload*.

---

## FASE 2: Perancangan Database (Migration & Models)

Buat tabel dan relasi berdasarkan model data di PRD Bab 9.
- [ ] **Tabel `users`:** Sesuaikan *migration* bawaan Breeze. Tambahkan kolom `role` (enum: admin, petugas, anggota), `anggota_id` (nullable, foreign key ke tabel anggota), dan `force_password_change` (boolean).
- [ ] **Tabel `anggota`:** Buat *migration* untuk id_anggota (unik, mis. TKD22-001), nama_lengkap, nim, tanggal_lahir, jenis_kelamin, no_whatsapp, foto_dobok, fakultas, program_studi, no_bpjs, dan qr_code.
- [ ] **Tabel `jadwal`:** Buat tabel untuk hari, jam_start, dan jam_close.
- [ ] **Tabel `hari_libur`:** Buat tabel untuk tanggal dan keterangan libur.
- [ ] **Tabel `absensi`:** Buat tabel dengan *foreign keys* (anggota_id, jadwal_id, petugas_id, izin_sakit_id), beserta kolom tanggal, status (hadir, izin, sakit, alfa), sumber, dan waktu_scan.
- [ ] **Tabel `izin_sakit`:** Buat tabel pengajuan perizinan (anggota_id, jadwal_id, tanggal, jenis, keterangan, bukti_lampiran, status_approval).
- [ ] **Tabel `pengaturan_profil`:** Buat tabel *singleton* untuk identitas organisasi (kop surat laporan).

---

## FASE 3: Konversi HTML ke Blade Templates (View UI)

Pecah 10 file HTML statis yang telah dibuat menjadi komponen-komponen Blade yang dapat digunakan kembali (*reusable*).
- [ ] **Buat Layout Utama (`app.blade.php`):**
  - Pisahkan elemen navigasi *Sidebar* (untuk desktop) dan *Header* (untuk mobile).
  - Buat komponen *Dynamic Sidebar* yang merender menu berbeda berdasarkan `auth()->user()->role`.
- [ ] **Konversi Halaman Admin/Petugas:**
  - `dashboard_admin.html` -> `resources/views/admin/dashboard.blade.php`
  - `absensi.html` -> `resources/views/petugas/absensi.blade.php` (Integrasikan Livewire untuk *scan* QR dan tabel *real-time*).
  - `database_anggota.html` -> `resources/views/admin/anggota/index.blade.php`
  - `perizinan.html` -> `resources/views/admin/perizinan/index.blade.php`
  - `manajemen_petugas.html` -> `resources/views/admin/petugas.blade.php`
  - `jadwal_latihan.html` -> `resources/views/admin/jadwal.blade.php`
  - `manajemen_hari_libur.html` -> `resources/views/admin/hari_libur.blade.php`
  - `rekap_kehadiran.html` -> `resources/views/admin/rekap.blade.php`
- [ ] **Konversi Halaman Anggota:**
  - `dashboard_anggota.html` -> `resources/views/anggota/dashboard.blade.php`
  - `pengaturan_anggota.html` -> `resources/views/anggota/pengaturan.blade.php`
- [ ] **Transisi JavaScript:** Ubah logika *dummy* JavaScript (DOM manipulation) di HTML statis menjadi logika backend (Blade data binding) atau interaktivitas Livewire / Alpine.js.

---

## FASE 4: Implementasi Logika Bisnis (Backend Logic)

- [ ] **Manajemen Akun (Auto-Provisioning):** 
  - Saat Admin menambahkan anggota baru, buat logika untuk meng-*generate* `ID Anggota` secara otomatis.
  - *Generate* QR Code unik berdasarkan `ID Anggota` dan simpan *path*-nya.
  - Otomatis buat data di tabel `users` (email = NIM@webmail.uad.ac.id, password default = bcrypt(NIM)).
- [ ] **Proses Absensi (Scan QR):**
  - Buat API atau *Livewire method* untuk menerima input dari scanner.
  - Validasi: Apakah berada dalam rentang `jam_start` dan `jam_close` jadwal? Apakah tanggal tersebut terdaftar di `hari_libur`?
  - Cegah absensi ganda (jika sudah hadir, tolak *scan* kedua).
- [ ] **Auto-Alfa (Scheduler / Cron Job):**
  - Buat `Console/Command` Laravel (misal: `absen:auto-alfa`) yang dijadwalkan berjalan via `Task Scheduler`.
  - Logika: Setelah `jam_close` pada hari latihan selesai, cari anggota yang belum memiliki entri di tabel `absensi` dan tidak memiliki `izin_sakit` berstatus *approved*, lalu set statusnya menjadi "Alfa".
- [ ] **Sistem Perizinan:**
  - Logika tolak otomatis: Jika anggota mengajukan izin kurang dari 2 jam sebelum `jam_start`, gagalkan validasi.
  - *Observer*: Saat Admin/Petugas menyetujui (`approve`) pengajuan, otomatis buat/update *record* di tabel `absensi` dengan status Izin/Sakit.
- [ ] **Rekapitulasi & Ekspor:**
  - Integrasikan pustaka `maatwebsite/excel` untuk mengekspor data hasil filter (tabel absensi harian dan rekap bulanan).
  - Gunakan data `pengaturan_profil` jika mengekspor dalam bentuk format kop surat.

---

## FASE 5: QA & Testing

- [ ] Lakukan pengujian Middleware (Role Base Access Control): Pastikan peran Anggota tidak bisa mengakses URL Admin.
- [ ] Uji responsivitas UI pada perangkat seluler (terutama untuk halaman Scan QR dan Dashboard Anggota).
- [ ] Simulasikan pemindaian QR menggunakan data *dummy*.
- [ ] Simulasikan eksekusi Scheduler `Auto-Alfa` dan validasi logika pembaruan otomatis perizinan.

---
*Dokumen ini diterbitkan berdasarkan PRD v0.3 dan desain antarmuka Soft UI.*