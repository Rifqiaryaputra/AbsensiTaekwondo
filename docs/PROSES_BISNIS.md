# DOKUMEN PROSES BISNIS
## Sistem Absensi Cerdas Berbasis QR Code
## Unit Kegiatan Mahasiswa (UKM) Taekwondo

|                 |                                                                 |
| --------------- | --------------------------------------------------------------- |
| **Nama Sistem** | Sistem Absensi Cerdas Berbasis QR Code UKM Taekwondo            |
| **Versi**       | 1.0                                                             |
| **Penyusun**    | System Analyst & Technical Writer                               |
| **Audien**      | Pengurus Organisasi, Admin, Petugas, Anggota UKM Taekwondo      |
| **Tanggal**     | Agustus 2026                                                     |

---

## 1. DESKRIPSI SINGKAT SISTEM

Sistem Absensi Cerdas Berbasis QR Code adalah aplikasi berbasis web yang digunakan untuk mendigitalkan proses pencatatan kehadiran latihan anggota UKM Taekwondo. Sistem ini menggantikan metode absensi manual (buku absen fisik) dengan pemindaian **QR Code unik** yang dimiliki setiap anggota melalui kamera perangkat petugas.

Setiap anggota mendapatkan **ID Card digital** berisi QR Code pribadi yang diterbitkan otomatis oleh sistem saat pendaftaran. Petugas yang bertugas pada jadwal tertentu cukup memindai QR Code tersebut untuk mencatat kehadiran secara instan. Sistem juga menyediakan mekanisme pengajuan izin/sakit secara mandiri oleh anggota, masa koreksi untuk perbaikan data kehadiran, serta rekapitulasi otomatis (status **Alfa**) bagi anggota yang tidak hadir tanpa keterangan.

Tujuan utama sistem ini adalah menciptakan pencatatan kehadiran yang **cepat, akurat, transparan**, dan mudah direkapitulasi untuk kebutuhan laporan pengurus UKM Taekwondo.

---

## 2. DEFINISI AKTOR

### 2.1 Admin
- **Deskripsi:** Pengurus inti UKM yang memiliki tanggung jawab penuh atas pengelolaan sistem.
- **Hak Akses:**
  - Mengelola data anggota (tambah, ubah, hapus, reset password).
  - Mengelola data petugas absensi (tunjuk, cabut, ubah data).
  - Mengelola jadwal latihan (buat, ubah, hapus) dan menentukan jam buka/tutup.
  - Mengelola hari libur nasional/insidental.
  - Melakukan absensi (scan QR) di **semua** jadwal tanpa batasan hari/jam.
  - Mengakses dan mengoreksi seluruh data kehadiran.
  - Memproses persetujuan/penolakan pengajuan izin/sakit anggota.
  - Melihat dan mengekspor rekap kehadiran.
  - Mengatur profil organisasi (logo, nama, alamat) untuk kop surat laporan.

### 2.2 Petugas
- **Deskripsi:** Anggota yang ditunjuk oleh Admin untuk menjalankan tugas absensi pada jadwal tertentu. Berasal dari data anggota yang **di-upgrade** perannya oleh Admin.
- **Hak Akses:**
  - Melakukan absensi (scan QR) **hanya pada jadwal di mana ia ditugaskan**, dan **hanya pada rentang jam buka–jam tutup** jadwal tersebut.
  - Mengakses masa koreksi untuk mengubah status kehadiran anggota pada jadwal yang ia tugaskan.
  - Melihat data anggota (read-only).
  - Memproses persetujuan/penolakan pengajuan izin/sakit anggota.
  - Melihat dan mengekspor rekap kehadiran.
- **Catatan:** Satu jadwal latihan dapat memiliki lebih dari satu petugas. Petugas tidak bisa mengakses menu Jadwal, Petugas Absensi, maupun Settings profil organisasi.

### 2.3 Anggota
- **Deskripsi:** Mahasiswa terdaftar sebagai peserta latihan UKM Taekwondo.
- **Hak Akses:**
  - Melihat **QR Code pribadi** dan **ID Card digital** pada halaman Dashboard.
  - Melihat statistik kehadiran pribadi (Hadir, Izin, Sakit, Alfa).
  - Mengajukan izin/sakit secara mandiri.
  - Membatalkan pengajuan izin/sakit selama status masih **Menunggu**.
  - Mengubah password akun sendiri melalui menu Settings.
- **Catatan:** Anggota adalah pengguna pasif dalam proses absensi — ia tidak perlu melakukan scan apa pun; cukup menunjukkan QR Code-nya kepada Petugas. Akun login Anggota dibuat otomatis oleh sistem saat Admin mendaftarkan anggota baru.

---

## 3. ALUR PROSES BISNIS UTAMA

### 3.A Proses Manajemen Anggota & QR Code

**Tujuan:** Mendaftarkan anggota baru ke dalam sistem sekaligus menerbitkan ID Anggota, QR Code unik, dan akun login secara otomatis.

**Langkah-langkah:**

1. **Admin login** ke sistem menggunakan email dan password.
2. Admin membuka menu **Data Anggota** dan memilih **Tambah Anggota**.
3. Admin mengisi formulir pendaftaran dengan data berikut:
   - Nama Lengkap
   - NIM (Nomor Induk Mahasiswa)
   - Tanggal Lahir
   - Jenis Kelamin
   - No. WhatsApp
   - Fakultas
   - Program Studi
   - No. BPJS *(opsional)*
   - Foto Berseragam Dobok (format PNG/JPG, maks. 2 MB)
4. Admin menekan tombol **Simpan**.
5. Sistem melakukan **validasi ketat**:
   - Memeriksa NIM apakah sudah terdaftar (tidak boleh duplikat).
   - Memeriksa kelengkapan field wajib.
   - Memvalidasi format data (email, nomor, tanggal).
6. Jika validasi lolos, sistem secara otomatis:
   - **Menghasilkan ID Anggota** unik dengan format `TKD{2 digit awal NIM}-{nomor urut 3 digit}`, contoh: `TKD22-001`.
   - **Mengenerate QR Code** unik berdasarkan ID Anggota tersebut dan menyimpannya sebagai gambar.
   - **Membuat akun login** (role Anggota) dengan:
     - Email: `{nim}@webmail.uad.ac.id`
     - Password default: **NIM anggota** (disimpan dalam bentuk terenkripsi/hash).
7. Sistem menampilkan konfirmasi sukses beserta pratinjau QR Code dan informasi akun login yang telah dibuat.
8. Admin dapat **mengunduh/cetak** QR Code untuk pembuatan ID Card fisik anggota.
9. Anggota kemudian dapat login menggunakan akun tersebut dan disarankan mengganti password default.

**Untuk Update Status Anggota (Alumni/Tidak Aktif):**
- Admin cukup mengubah status anggota di halaman Data Anggota.
- Proses validasi bersifat **longgar**: tidak ada pembuatan akun baru atau regenerasi QR Code.
- Anggota dengan status tidak aktif tetap memiliki data historis kehadiran yang tersimpan.

---

### 3.B Proses Pembuatan Jadwal & Penugasan Petugas

**Tujuan:** Menentukan hari dan jam latihan serta menunjuk petugas yang bertanggung jawab melakukan absensi pada setiap sesi.

**Langkah-langkah:**

1. **Admin login** ke sistem dan membuka menu **Jadwal**.
2. Admin memilih **Tambah Jadwal**.
3. Admin menentukan:
   - **Hari** dalam seminggu (dapat memilih lebih dari satu hari, misal: Senin, Rabu, Jumat).
   - **Jam Buka (Jam Start):** Waktu dimulainya sesi absensi.
   - **Jam Tutup (Jam Close):** Waktu berakhirnya sesi absensi.
4. Admin menyimpan jadwal.
5. Selanjutnya, Admin membuka menu **Petugas Absensi**.
6. Admin memilih **Tambah Petugas**.
7. Admin **mencari anggota** berdasarkan nama, NIM, atau ID Anggota — sistem hanya menampilkan data anggota yang sudah terdaftar.
8. Admin memilih anggota yang dituju dan **menetapkan jadwal** di mana anggota tersebut bertugas (satu petugas bisa ditugaskan pada satu atau beberapa jadwal).
9. Sistem **mengubah role** akun anggota tersebut menjadi **Petugas** (tanpa membuat akun baru).
10. Anggota yang bersangkutan kini dapat login sebagai Petugas dan akan melihat menu absensi **hanya pada jadwal** yang telah ditugaskan kepadanya.

**Aturan penugasan:**
- Satu jadwal dapat memiliki **banyak petugas**.
- Satu petugas dapat ditugaskan pada **banyak jadwal**.
- Petugas **hanya bisa melakukan scan** pada jadwal yang ditugaskan dan dalam rentang Jam Buka–Jam Tutup.

---

### 3.C Proses Pelaksanaan Absensi (Reguler)

**Tujuan:** Mencatat kehadiran anggota pada sesi latihan yang sedang berlangsung melalui pemindaian QR Code.

**Langkah-langkah:**

1. **Petugas login** ke sistem pada hari dan jam latihan sesuai jadwal yang ditugaskan.
2. Petugas membuka menu **Absen**.
3. Sistem menampilkan jadwal yang sedang **aktif** (dalam rentang Jam Buka–Jam Tutup). Jika tidak ada jadwal aktif, sistem menampilkan pesan bahwa tidak ada sesi absensi saat ini.
4. Anggota yang hadir menunjukkan **QR Code** (dari ID Card fisik atau dari halaman Dashboard akun masing-masing).
5. Petugas mengarahkan kamera perangkat ke QR Code anggota.
6. Sistem melakukan validasi:
   - Apakah QR Code valid dan terdaftar di sistem?
   - Apakah anggota tersebut belum tercatat absen pada jadwal ini? *(cegah absensi ganda)*
   - Apakah tanggal hari ini bukan hari libur?
7. Jika validasi lolos, sistem mencatat kehadiran dengan status **Hadir** dan menampilkan notifikasi su sukses beserta data anggota (nama, ID Anggota).
8. Petugas mengulangi langkah 4–7 untuk anggota berikutnya.
9. **Alternatif Input Manual:** Jika QR Code rusak atau tidak terbaca, Petugas dapat memasukkan **NIM** anggota secara manual melalui form input yang tersedia.
10. Sistem menolak proses absensi apabila dilakukan **di luar Jam Buka–Jam Tutup** jadwal, dengan menampilkan pesan error.

**Kondisi khusus:**
- Jika Petugas belum login atau tidak ditugaskan pada jadwal hari ini, menu Absen tidak menampilkan jadwal apa pun.
- Jika Admin melakukan absensi, Admin dapat mengakses **semua jadwal** tanpa batasan hari/jam.

---

### 3.D Proses Masa Koreksi

**Tujuan:** Memberikan kesempatan kepada Petugas (dan Admin) untuk memperbaiki atau mengubah status kehadiran anggota setelah sesi latihan selesai, namun sebelum data terkunci secara permanen.

**Langkah-langkah:**

1. Jam latihan telah **tutup** (melewati Jam Close).
2. Sistem memasuki **Masa Koreksi** yang berlangsung **hingga pukul 21.00 pada hari berikutnya** setelah tanggal jadwal.
3. Petugas yang bertugas (atau Admin) membuka menu **Absen** atau **Rekap Kehadiran**.
4. Sistem menampilkan daftar kehadiran pada jadwal yang sudah lewat, dengan status yang masih dapat diubah (belum terkunci).
5. Petugas/Admin memilih anggota yang akan dikoreksi statusnya.
6. Petugas/Admin mengubah status kehadiran menjadi salah satu dari: **Hadir, Izin, Sakit, atau Alfa**.
7. Sistem mencatat perubahan beserta **siapa yang melakukan koreksi** dan **waktu koreksi** (audit trail).
8. **Setelah pukul 21.00 hari berikutnya**, data kehadiran sesi tersebut **terkunci otomatis** dan tidak dapat diubah lagi.
9. Masa koreksi juga mencakup situasi di mana **pengajuan izin/sakit disetujui** oleh Admin/Petugas — sistem otomatis memperbarui status kehadiran anggota pada tanggal terkait menjadi Izin atau Sakit tanpa perlu input manual.

**Aturan penting dalam masa koreksi:**
- Hanya Petugas yang **ditugaskan pada jadwal tersebut** yang bisa melakukan koreksi.
- Admin dapat melakukan koreksi pada **semua jadwal** tanpa batasan.
- Setiap perubahan tercatat dalam sistem (siapa, kapan, perubahan dari status apa ke status apa).

---

### 3.E Proses Rekapitulasi Otomatis (Auto-Alfa)

**Tujuan:** Memastikan setiap anggota memiliki status kehadiran pada setiap sesi jadwal yang sudah lewat, tanpa terkecuali. Anggota yang tidak tercatat hadir dan tidak memiliki izin/sakit akan otomatis mendapat status **Alfa** (Tidak Hadir Tanpa Keterangan).

**Langkah-langkah:**

1. Sistem menjalankan **proses terjadwal (cron job / scheduler)** secara otomatis di server.
2. Proses ini dipicu **setelah Jam Tutup (Jam Close)** suatu jadwal terlewati.
3. Sistem mengambil daftar **seluruh anggota aktif** yang terdaftar di UKM.
4. Untuk setiap anggota, sistem memeriksa:
   - Apakah anggota sudah memiliki **catatan kehadiran** (Hadir, Izin, atau Sakit) pada sesi jadwal tersebut?
   - Apakah anggota memiliki **pengajuan izin/sakit yang disetujui** pada tanggal tersebut?
5. Jika **tidak ada catatan kehadiran sama sekali** dan **tidak ada izin/sakit yang disetujui**, maka sistem secara otomatis:
   - Membuat catatan absensi baru dengan status **Alfa** untuk anggota tersebut pada jadwal terkait.
   - Mencatat sumber sebagai **"Otomatis"** untuk membedakan dari catatan manual.
6. Proses berjalan untuk **semua anggota** dan **semua sesi jadwal** yang sudah lewat namun belum diproses.
7. Data Alfa yang dihasilkan tetap dapat dikoreksi oleh Petugas/Admin selama masih dalam **Masa Koreksi** (hingga pukul 21.00 hari berikutnya).
8. Sistem memastikan proses ini **idempotent** — tidak akan membuat duplikasi atau menjalankan ulang untuk jadwal yang sudah diproses.

**Ilustrasi sederhana:**
| Situasi | Hasil |
|---|---|
| Anggota A discan Hadir oleh Petugas | Tetap **Hadir** |
| Anggota B mengajukan Izin dan disetujui | Status **Izin** (tidak berubah) |
| Anggota C tidak discan, tidak punya izin | Otomatis **Alfa** |
| Anggota D tidak discan, punya izin tertolak | Otomatis **Alfa** |

---

## 4. BUSINESS RULES (ATURAN BISNIS MUTLAK)

Berikut adalah aturan-aturan mutlak dalam sistem yang **tidak boleh dilanggar** dan harus diimplementasikan secara konsisten:

### 4.1 Aturan Keamanan & Akses

| No. | Aturan | Keterangan |
|----|--------|------------|
| BR-01 | **Setiap pengguna wajib login** untuk mengakses sistem. Tidak ada akses publik ke halaman internal. | |
| BR-02 | **Pembatasan akses berbasis peran** (Role-Based Access Control) diterapkan di sisi server/backend, bukan hanya di tampilan. Pengguna tidak bisa mengakses URL/modul di luar hak aksesnya. | |
| BR-03 | Admin memiliki akses penuh ke seluruh modul dan data. | |
| BR-04 | Petugas hanya bisa mengakses menu Absen dan melakukan scan pada **jadwal di mana ia ditugaskan**, dalam **rentang Jam Buka–Jam Tutup**, atau dalam **Masa Koreksi** untuk jadwal tersebut. | |
| BR-05 | Anggota hanya bisa melihat Dashboard (QR Code & statistik sendiri) dan Settings (ubah password sendiri). Anggota **tidak bisa** melihat data anggota lain, jadwal, atau rekap kehadiran umum. | |

### 4.2 Aturan Manajemen Anggota

| No. | Aturan | Keterangan |
|----|--------|------------|
| BR-06 | **NIM bersifat unik** — tidak boleh ada dua anggota dengan NIM yang sama. | |
| BR-07 | **ID Anggota** digenerate otomatis oleh sistem dengan format `TKD{2 digit awal NIM}-{nomor urut 3 digit}` (contoh: `TKD22-001`). Tidak bisa diubah manual. | |
| BR-08 | **QR Code** digenerate otomatis oleh sistem berbasis ID Anggota dan tidak bisa diunggah/ditentukan manual. | |
| BR-09 | Saat anggota baru ditambahkan, sistem **wajib** membuat akun login (role Anggota) secara otomatis. | |
| BR-10 | Saat anggota dijadikan Petugas, sistem **wajib** mengubah role akun yang sudah ada — **tidak membuat akun baru**. | |
| BR-11 | Saat anggota dinonaktifkan sebagai Petugas, role akun dikembalikan menjadi Anggota. Data kehadiran yang pernah dicatat oleh petugas tersebut tetap tersimpan. | |

### 4.3 Aturan Jadwal & Hari Libur

| No. | Aturan | Keterangan |
|----|--------|------------|
| BR-12 | **Jam Buka (Jam Start)** tidak boleh sama atau lebih besar dari **Jam Tutup (Jam Close)**. | |
| BR-13 | Sistem **tidak membuka sesi absensi** pada tanggal yang terdaftar sebagai Hari Libur. | |
| BR-14 | Satu hari dapat memiliki lebih dari satu jadwal (misal: latihan pagi dan sore), masing-masing dengan Jam Buka dan Jam Tutup yang berbeda. | |
| BR-15 | Jadwal bersifat **mingguan berulang** — berlaku setiap minggu pada hari yang sama, kecuali hari libur. | |

### 4.4 Aturan Proses Absensi

| No. | Aturan | Keterangan |
|----|--------|------------|
| BR-16 | **Scan QR Code hanya dapat dilakukan dalam rentang Jam Buka–Jam Tutup** jadwal, atau dalam Masa Koreksi. | |
| BR-17 | **Tidak boleh ada absensi ganda** — satu anggota hanya memiliki satu status kehadiran per sesi jadwal per hari. | |
| BR-18 | **Input manual (NIM)** hanya tersedia sebagai opsi cadangan jika QR Code tidak terbaca. | |
| BR-19 | Status kehadiran yang valid: **Hadir, Izin, Sakit, Alfa**. Tidak ada status lain. | |
| BR-20 | Setiap pencatatan kehadiran wajib mencatat **sumber** (Scan/Manual/Otomatis/Izin Disetujui) untuk auditabilitas. | |

### 4.5 Aturan Perizinan

| No. | Aturan | Keterangan |
|----|--------|------------|
| BR-21 | Pengajuan izin/sakit **hanya dapat diajukan oleh Anggota** (untuk dirinya sendiri), bukan oleh Petugas atau Admin atas nama anggota. | |
| BR-22 | Pengajuan izin/sakit yang diajukan **kurang dari 2 jam sebelum Jam Buka (Jam Start)** jadwal akan **ditolak otomatis** oleh sistem. | |
| BR-23 | Anggota **dapat membatalkan** pengajuannya **hanya selama status masih "Menunggu"**. Setelah disetujui/ditolak, pembatalan tidak bisa dilakukan. | |
| BR-24 | Admin dan Petugas dapat menyetujui (**Approve**) atau menolak (**Reject**) pengajuan izin/sakit. | |
| BR-25 | **Jika pengajuan disetujui**, sistem **otomatis membuat/memperbarui** catatan kehadiran anggota pada tanggal terkait menjadi **Izin** atau **Sakit** — tanpa perlu input manual oleh Petugas. | |

### 4.6 Aturan Masa Koreksi & Auto-Alfa

| No. | Aturan | Keterangan |
|----|--------|------------|
| BR-26 | **Masa Koreksi** berlangsung sejak Jam Tutup hingga **pukul 21.00 pada hari berikutnya** setelah tanggal jadwal. | |
| BR-27 | **Setelah pukul 21.00 hari berikutnya**, data kehadiran sesi tersebut **terkunci permanen** dan tidak bisa diubah lagi. | |
| BR-28 | Hanya Petugas yang **ditugaskan pada jadwal tersebut** (dan Admin) yang bisa melakukan koreksi status kehadiran. | |
| BR-29 | **Auto-Alfa** dijalankan oleh sistem secara otomatis setelah Jam Tutup terlewati — tanpa intervensi manual. | |
| BR-30 | Auto-Alfa **tidak akan mengubah** anggota yang sudah memiliki catatan kehadiran (Hadir/Izin/Sakit) — hanya mengisi yang kosong. | |
| BR-31 | Data Alfa hasil auto-rekap dapat dikoreksi oleh Petugas/Admin selama masih dalam Masa Koreksi. | |
| BR-32 | Proses Auto-Alfa harus **idempotent** — jika dijalankan ulang untuk jadwal yang sama, tidak membuat duplikasi data. | |

### 4.7 Aturan Data & Rekapitulasi

| No. | Aturan | Keterangan |
|----|--------|------------|
| BR-33 | Semua data kehadiran (termasuk hasil koreksi) tercatat dengan **audit trail** — siapa yang mencatat/mengubah, kapan, dan dari sumber apa. | |
| BR-34 | Rekap kehadiran dapat difilter berdasarkan **rentang tanggal** dan diekspor ke format **Excel**. | |
| BR-35 | Data anggota yang dihapus **tidak menghilangkan** catatan kehadiran historisnya — data absensi tetap tersimpan. | |

---

*Dokumen ini disusun sebagai panduan proses bisnis Sistem Absensi Cerdas Berbasis QR Code UKM Taekwondo. Diharapkan seluruh pengurus, Admin, Petugas, dan Anggota memahami alur dan aturan yang telah ditetapkan agar sistem dapat berjalan dengan optimal.*