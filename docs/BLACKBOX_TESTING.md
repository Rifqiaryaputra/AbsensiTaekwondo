# BAB PENGUJIAN SISTEM
## Pengujian Blackbox — Sistem Absensi Cerdas Berbasis QR Code
### Unit Kegiatan Mahasiswa (UKM) Taekwondo

|                 |                                                            |
| --------------- | ---------------------------------------------------------- |
| **Nama Sistem** | Sistem Absensi Cerdas Berbasis QR Code UKM Taekwondo       |
| **Metode**      | Blackbox Testing (fungsional, tanpa melihat struktur kode) |
| **Fokus**       | Boundary Time & Role-Based Access Control (RBAC)           |
| **Penyusun**    | Quality Assurance Engineer                                  |
| **Tanggal**     | Agustus 2026                                                |

---

## 1. PENDAHULUAN

Pengujian Blackbox dilakukan untuk memverifikasi bahwa sistem berperilaku sesuai
spesifikasi fungsional, khususnya pada **logika validasi waktu (boundary time)** dan
**pembatasan hak akses berbasis peran (Role-Based Access Control)**. Penguji tidak
memerlukan pengetahuan struktur internal kode; pengamatan dilakukan semata-mata
melalui antarmuka aplikasi (input, pemilihan menu, dan respons yang ditampilkan).

### 1.1 Data Referensi Pengujian (Fixture)

| Elemen | Nilai Acuan |
|---|---|
| Jadwal Uji | Hari **Rabu**, Jam Buka `19:00`, Jam Tutup `21:00` |
| Petugas A | Terdaftar pada `jadwal_petugas` untuk jadwal Rabu |
| Petugas B | Tidak terdaftar pada jadwal Rabu (tidak memiliki tugas) |
| Admin | Peran `admin`, tidak terikat batasan jadwal |
| Anggota Aktif | Berstatus `aktif`, memiliki QR Code valid |
| Anggota Non-Aktif | Berstatus `non-aktif` / `alumni` |

### 1.2 Analisis Nilai Batas (Boundary Value)

Berdasarkan rentang jadwal `19:00 – 21:00`, nilai batas waktu ditetapkan sebagai berikut:

| Kategori | Nilai Waktu | Kondisi Ruang/Waktu | Status Sesi |
|---|---|---|---|
| **B1 ≤ 0** | `18:59:59` | Waktu < `jam_start` | Belum Mulai |
| **B2 = 0** | `19:00:00` | Waktu = `jam_start` (inklusif `<=`) | **Aktif** |
| **B3 = Tengah** | `20:00:00` | Di antara `jam_start` dan `jam_close` | Aktif |
| **B4 = Maks** | `21:00:00` | Waktu = `jam_close` (inklusif `>=`) | **Aktif** |
| **B5 > Maks** | `21:00:01` | Waktu > `jam_close` | Ditutup / Masuk Koreksi |
| **B6 Koreksi Awal** | Hari berikutnya `12:00:00` | Awal jendela koreksi (inklusif) | Masa Koreksi |
| **B7 Koreksi Akhir** | Hari berikutnya `13:00:00` | Akhir jendela koreksi (inklusif `between`) | Masa Koreksi |
| **B8 Terkunci** | Hari berikutnya `13:00:01` | Setelah jendela koreksi | Terkunci |

> **Catatan:** Pada `ValidatorService::getActiveJadwal`, batas buka dan tutup bersifat
> **inklusif** (`jam_start <= now` dan `jam_close >= now`), sehingga tepat pada jam
> buka dan jam tutup sesi masih dianggap aktif.

---

## 2. MODUL AUTENTIKASI & OTORISASI HALAMAN (RBAC)

**Prinsip:** Aktor hanya dapat mengakses halaman sesuai peran; pengecekan otorisasi
dilakukan oleh middleware `role:` dan method `canManageJadwal()`.

| ID | Skenario Pengujian | Pra-Kondisi / Batasan | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|---|
| **TC-AUTH-01** | Admin dapat mengakses seluruh menu. | Login sebagai **Admin**. Masa kepengurusan aktif. | 1. Login dengan akun Admin. 2. Akses menu Dashboard, Absensi, Data Anggota, Petugas, Jadwal, Hari Libur, Perizinan, Rekap, Settings. | Admin berhasil membuka **keseluruhan** menu tanpa pemblokiran. Tidak ada halaman yang memunculkan status 403. |
| **TC-AUTH-02** | Petugas dapat mengakses menu yang diizinkan perannya. | Login sebagai **Petugas**. | 1. Login dengan akun Petugas. 2. Akses menu Dashboard, Absensi, Data Anggota, Hari Libur, Perizinan, Rekap. 3. Coba akses menu **Petugas**, **Jadwal**, dan **Settings**. | Menu Dashboard, Absensi, Data Anggota, Hari Libur, Perizinan, Rekap dapat dibuka. Menu **Petugas, Jadwal, Settings** tidak dapat diakses (muncul 403 / tidak tampil di sidebar). |
| **TC-AUTH-03** | Anggota dilarang mengakses halaman internal Admin/Petugas. | Login sebagai **Anggota**. | 1. Login dengan akun Anggota. 2. Akses URL `/absensi` secara langsung di address bar. | Pengguna diarahkan (redirect) ke **Dashboard Anggota** (`/dashboard-anggota`). Halaman `/absensi` tidak dirender. |
| **TC-AUTH-04** | Pengguna tidak login dilarang masuk halaman internal. | **Belum login** (guest). | 1. Akses URL `/absensi` tanpa login. | Diarahkan ke halaman **login**. Konten halaman internal tidak ditampilkan. |
| **TC-AUTH-05** | Petugas tidak terdaftar pada jadwal dilarang membuka halaman absensi. | Login sebagai **Petugas B** (tidak terdaftar di `jadwal_petugas` jadwal Rabu). Waktu sesi aktif `19:30`. | 1. Login sebagai Petugas B. 2. Buka menu **Absensi** saat jadwal Rabu sedang berlangsung. | Sistem tidak menampilkan antarmuka scanner aktif; berdasarkan aturan sistem muncul pesan **"Anda tidak memiliki jadwal tugas"** (otorisasi `canManageJadwal` bernilai `false`). Tidak ada data absensi yang dapat diproses. |
| **TC-AUTH-06** | Petugas terdaftar pada jadwal dapat membuka halaman absensi. | Login sebagai **Petugas A** (terdaftar pada `jadwal_petugas` jadwal Rabu). Waktu aktif `19:30`. | 1. Login sebagai Petugas A. 2. Buka menu **Absensi**. | Antarmuka scanner terbuka dan Petugas A **berwenang** memproses absensi pada jadwal tersebut. |
| **TC-AUTH-07** | Admin dapat mengakses halaman absensi pada jadwal mana pun. | Login sebagai **Admin**. Waktu aktif `19:30`. | 1. Login sebagai Admin. 2. Buka menu **Absensi** pada jadwal Rabu (dan jadwal lain). | Admin **selalu** berwenang; `canManageJadwal` mengembalikan `true` tanpa pengecekan `jadwal_petugas`. Scanner terbuka pada semua jadwal. |

**Catatan QA:** Pada implementasi, ketika tidak ada jadwal tugas, antarmuka menampilkan
pesan (dari `processScanInput`/`processManualInput`): **"Anda tidak memiliki jadwal tugas
absen pada saat ini."** Kalimat ini sedikit berbeda dari ketentuan dokumen
("Anda tidak memiliki jadwal tugas"). Diharapkan pesan diseragamkan dengan spesifikasi
agar konsisten.

---

## 3. MODUL PEMINDAIAN ABSENSI (LOGIKA WAKTU)

**Prinsip:** Scanner hanya aktif pada rentang `jam_start ≤ waktu ≤ jam_close`.
Di luar rentang tersebut scanner ditutup atau beralih ke tabel Masa Koreksi.

### 3.1 Skenario Waktu < Jam Buka (Belum Mulai)

| ID | Skenario Pengujian | Pra-Kondisi / Batasan | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|---|
| **TC-ABS-01** | Scanner ditutup sebelum jam buka (batas bawah). | Login sebagai **Petugas A**. Waktu saat ini **`18:59:59`** (sebelum `19:00`). | 1. Login Petugas A. 2. Buka menu **Absensi**. 3. Amati status scanner & coba scan QR. | Scanner **tidak aktif/tersembunyi**. Sistem menampilkan status jadwal **"belum dibuka"**. Percobaan scan tidak dapat memproses absensi (data tidak tercatat). |
| **TC-ABS-02** | Scanner tertutup tepat pada batas sebelum jam buka. | Seperti TC-ABS-01 tetapi waktu **`19:00:00`** (tepat jam buka). | 1. Login Petugas A. 2. Buka menu Absensi. 3. Scan QR anggota valid. | Sesi **aktif** (batas inklusif). Scanner terbuka, absensi Hadir berhasil dicatat. *(Verifikasi bahwa batas buka bersifat inklusif.)* |

### 3.2 Skenario Waktu Aktif (Di Antara Jam Buka & Tutup)

| ID | Skenario Pengujian | Pra-Kondisi / Batasan | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|---|
| **TC-ABS-03** | Scan QR valid pada waktu aktif. | Login sebagai **Petugas A**. Waktu **`20:00`** (tengah sesi). Terdapat Anggota aktif dengan QR valid. | 1. Login Petugas A. 2. Buka menu Absensi. 3. Arahkan QR Code milik anggota ke kamera. | Sistem mencocokkan QR, mencetak status **"Hadir"** ke database, dan menampilkan notifikasi **berhasil**. Record absensi baru muncul pada tabel. |
| **TC-ABS-04** | Scan QR pada batas waktu tutup (inklusif). | Waktu **`21:00:00`** (tepat jam tutup). Anggota aktif dengan QR valid. | 1. Login Petugas A. 2. Scan QR pada `21:00:00`. | Sesi masih **aktif** (batas inklusif). Absensi Hadir berhasil dicatat. *(Verifikasi batas tutup inklusif.)* |
| **TC-ABS-05** | Input NIM manual pada waktu aktif. | Waktu **`20:00`**. Anggota aktif dengan NIM terdaftar. | 1. Login Petugas A. 2. Pada input manual, masukkan NIM yang valid. 3. Simpan. | Sistem mencatat status **"Hadir"** dengan sumber `manual`. Notifikasi berhasil ditampilkan. |
| **TC-ABS-06** | Scan pada waktu libur. | Tanggal hari ini terdaftar pada tabel `hari_libur`. | 1. Login Petugas A. 2. Buka menu Absensi. 3. Scan QR. | Sistem menampilkan keterangan **hari libur**; scanner **tidak dibuka**. Tidak ada absensi yang dapat diproses. |
| **TC-ABS-07** | Scan duplikat (absensi ganda) pada sesi sama. | Waktu **`20:00`**. Anggota X sudah tercatat Hadir pada sesi ini. | 1. Scan QR anggota X (yang sudah tercatat). | Sistem **menolak**: tampil pesan "sudah tercatat pada sesi ini". Tidak ada data duplikat yang dibuat (kendala unik `anggota_id + jadwal_id + tanggal`). |
| **TC-ABS-08** | Scan QR untuk anggota non-aktif. | Waktu **`20:00`**. Anggota berstatus `non-aktif`. | 1. Scan QR anggota non-aktif. | Sistem **menolak** dengan pesan "berstatus non-aktif" / "tidak dapat diabsen". Status absensi tidak tercatat. |
| **TC-ABS-09** | Input NIM untuk NIM tidak terdaftar. | Waktu **`20:00`**. | 1. Masukkan NIM yang tidak ada di database. 2. Simpan. | Sistem menampilkan pesan **"NIM tidak ditemukan"**. Tidak ada data absensi dibuat. |
| **TC-ABS-10** | Scan QR oleh Petugas B yang tidak bertugas. | Waktu **`20:00`**. Login sebagai **Petugas B**. | 1. Login Petugas B. 2. Coba scan QR. | Proses ditolak; pesan otorisasi **"Anda tidak memiliki jadwal tugas"**. Data tidak tercatat. |

### 3.3 Skenario Waktu > Jam Tutup (Ditutup)

| ID | Skenario Pengujian | Pra-Kondisi / Batasan | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|---|
| **TC-ABS-11** | Scanner disembunyikan setelah jam tutup. | Login sebagai **Petugas A**. Waktu **`21:00:01`** (setelah tutup). | 1. Login Petugas A. 2. Buka menu Absensi. | Kamera scanner **disembunyikan total**. UI beralih ke tampilan **Masa Koreksi / status "sudah ditutup"**. Tidak ada proses scan baru yang dapat dilakukan. |
| **TC-ABS-12** | Scanner tidak dapat melakukan absensi pada masa koreksi. | Waktu **`21:00:01`** (masa koreksi). | 1. Login Petugas A. 2. Coba scan QR. | Scan **ditolak**; sistem tidak mencatat status Hadir baru. Hanya manipulasi status yang diizinkan pada masa koreksi. |

---

## 4. MODUL MASA KOREKSI (MANIPULASI DATA)

**Prinsip:** Pengubahan status kehadiran hanya dapat dilakukan pada **jendela koreksi**
(hari berikutnya `12:00–13:00` bagi Petugas; **kapan pun** bagi Admin), dan hanya
pada jadwal terkait.

| ID | Skenario Pengujian | Pra-Kondisi / Batasan | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|---|
| **TC-KOR-01** | Koreksi tidak muncul sebelum jadwal ditutup. | Login sebagai **Petugas A**. Waktu **`20:00`** (sesi masih aktif). | 1. Login Petugas A. 2. Bukar menu Absensi. 3. Amati apakah ada tabel Masa Koreksi. | Tabel Masa Koreksi **belum ditampilkan**; UI masih dalam mode pemindaian (live). Opsi ubah status **tidak tersedia**. |
| **TC-KOR-02** | Koreksi Alfa → Hadir oleh Petugas pada jendela koreksi. | Petugas A login. Waktu **hari berikutnya `12:00:00`** (batas awal koreksi). Terdapat record anggota berstatus **Alfa**. | 1. Login Petugas A. 2. Buka Absensi. 3. Pilih record Alfa. 4. Ubah status menjadi **Hadir**. | Status record berubah menjadi **Hadir**, sumber `manual`, dan tercatat siapa yang mengubah. Notifikasi "Status Diperbarui" tampil. |
| **TC-KOR-03** | Koreksi Alfa → Sakit / Izin. | Waktu **`12:30`** (di dalam jendela). Record berstatus Alfa. | 1. Pilih record Alfa. 2. Ubah ke **Sakit**, lalu ke **Izin**. | Status berhasil diubah ke **Sakit** dan **Izin** sesuai pilihan. Perubahan tersimpan ke database. |
| **TC-KOR-04** | Koreksi oleh Petugas yang tidak bertugas pada jadwal. | Login sebagai **Petugas B** (tidak terdaftar di `jadwal_petugas`). | 1. Buka Absensi pada jadwal Rabu. 2. Coba ubah status record. | Pengubahan **ditolak** (`abort 403` atau pesan otorisasi). Data tidak berubah. |
| **TC-KOR-05** | Koreksi oleh Admin pada jadwal mana pun. | Login sebagai **Admin**. | 1. Buka Absensi jadwal Rabu. 2. Ubah status record (Alfa → Hadir/Sakit/Izin). | Perubahan **berhasil**. Admin tidak dibatasi jendela waktu koreksi (`canEditAbsensi` mengembalikan `true`). |
| **TC-KOR-06** | Koreksi di luar jendela waktu (terkunci). | Login sebagai **Petugas A**. Waktu **`13:00:01`** hari berikutnya (selepas batas koreksi). | 1. Buka Absensi. 2. Coba ubah status record. | Pengubahan **ditolak**; data sudah **terkunci**. Status record tidak berubah. |
| **TC-KOR-07** | Koreksi tepat pada batas akhir jendela. | Waktu **`13:00:00`** (batas akhir inklusif `between`). | 1. Buka Absensi. 2. Ubah status record. | Pengubahan **diizinkan** pada `13:00:00` karena batas bersifat inklusif. *(Temuan: perlu kepastian apakah batas akhir seharusnya eksklusif.)* |

**Catatan QA (Temuan):** Terdapat **ketidaksesuaian** antara dokumen PRD (masa koreksi
hingga **pukul 21.00** hari berikutnya) dengan implementasi (jendela koreksi Petugas
pada **`12:00–13:00`** hari berikutnya). Perlu konsistensi antara spesifikasi
dan implementasi, serta audit batas inklusif/eksklusif pada `13:00`.

---

## 5. MODUL REKAP OTOMATIS (CRON JOB AUTO-ALFA)

**Prinsip:** Setelah jadwal melewati jam tutup, sistem otomatis memberi status **Alfa**
bagi anggota yang tidak memiliki catatan kehadiran dan tidak memiliki izin/sakit yang
disetujui.

| ID | Skenario Pengujian | Pra-Kondisi / Batasan | Langkah Pengujian | Hasil yang Diharapkan |
|---|---|---|---|---|
| **TC-ALF-01** | Auto-Alfa memberi status Alfa pada anggota tanpa kehadiran. | Setelah **`21:00`** pada hari jadwal. Terdapat anggota A (tidak discan, tanpa izin) dan anggota B (Hadir). | 1. Jalankan command `php artisan absen:auto-alfa`. 2. Periksa tabel absensi. | Anggota A mendapatkan record **Alfa** (sumber `otomatis`). Anggota B tetap **Hadir** (tidak dipengaruhi). |
| **TC-ALF-02** | Auto-Alfa melewati anggota yang memiliki izin/sakit disetujui. | Anggota C memiliki pengajuan **izin disetujui** pada tanggal tersebut. | 1. Jalankan `php artisan absen:auto-alfa`. 2. Periksa status anggota C. | Anggota C **tidak** dibuat Alfa; status tetap **Izin/Sakit** sesuai pengajuan yang disetujui. |
| **TC-ALF-03** | Auto-Alfa hanya menyasar anggota berstatus aktif. | Anggota D berstatus `non-aktif` / `alumni` tanpa kehadiran. | 1. Jalankan `php artisan absen:auto-alfa`. | Anggota D **tidak** dibuat Alfa (hanya anggota `aktif` yang diproses). |
| **TC-ALF-04** | Auto-Alfa tidak berjalan pada hari libur. | Tanggal hari ini terdaftar pada `hari_libur`. | 1. Jalankan `php artisan absen:auto-alfa`. | Command menghasilkan pesan *"Hari ini merupakan hari libur, auto-alfa dilewati."* Tidak ada record Alfa yang dibuat. |
| **TC-ALF-05** | Auto-Alfa tidak berjalan sebelum jam tutup. | Waktu **`20:00`** (sesi masih aktif). | 1. Jalankan `php artisan absen:auto-alfa`. | Tidak ada jadwal selesai; pesan *"Belum ada jadwal hari ini yang sesi absensinya selesai."* Tidak ada record Alfa (jika jadwal hari ini belum ditutup). |
| **TC-ALF-06** | Auto-Alfa idempotent (tidak duplikasi). | Setelah `21:00`. Auto-Alfa telah dijalankan sekali. | 1. Jalankan `php artisan absen:auto-alfa` dua kali. | Pada eksekusi kedua, anggota yang sudah Alfa **tidak** dibuat duplikat; kendala unik terjaga. Jumlah record tidak bertambah ganda. |
| **TC-ALF-07** | Tutup manual menghasilkan rekap Alfa yang sama (fallback cron). | Waktu **`20:30`**. Admin menutup sesi via tombol **Tutup Absen**. | 1. Login Admin. 2. Buka Jadwal. 3. Klik **Tutup** pada jadwal aktif. | Anggota tanpa kehadiran & tanpa izin disetujui otomatis menjadi **Alfa**; `is_closed` berubah `true`. Pesan konfirmasi sukses tampil. |
| **TC-ALF-08** | Tutup manual pada jadwal yang sudah ditutup (duplikat tutup). | Jadwal sudah di-`close` (`is_closed = true`). | 1. Login Admin. 2. Klik **Tutup** ulang. | Sistem menolak: pesan *"Sesi absensi jadwal ini sudah ditutup."* Tidak ada data baru ditambahkan. |

---

## 6. MATRIKS RINGKASAN HASIL PENGUJIAN

| Modul | Jumlah Skenario | Status Uji |
|---|---|---|
| Autentikasi & Otorisasi (RBAC) | 7 (TC-AUTH-01 s.d. 07) | ⚠ Perlu konsistensi pesan (TC-AUTH-05) |
| Pemindaian Absensi (Boundary Time) | 12 (TC-ABS-01 s.d. 12) | ✔ Sesuai spesifikasi |
| Masa Koreksi | 7 (TC-KOR-01 s.d. 07) | ⚠ Temuan ketidaksesuaian jendela waktu (TC-KOR-06/07) |
| Rekap Otomatis (Auto-Alfa) | 8 (TC-ALF-01 s.d. 08) | ✔ Sesuai spesifikasi |

---

## 7. TEMUAN QA & REKOMENDASI

| No. | Temuan | Lokasi | Dampak | Rekomendasi |
|---|---|---|---|---|
| 1 | Pesan saat petugas tanpa jadwal tugas berbeda antara implementasi dan spesifikasi. | `processScanInput` / `processManualInput` | Kecil — pesan tidak konsisten dengan dokumen | Samakan teks pesan dengan ketentuan: **"Anda tidak memiliki jadwal tugas."** |
| 2 | Jendela koreksi Petugas diimplementasikan `12:00–13:00` hari berikutnya, sementara PRD menyatakan hingga **21.00**. | `canEditAbsensi()` | Sedang — ketidaksesuaian proses bisnis | Sepakati satu nilai; sesuaikan kode atau perbarui PRD agar konsisten. |
| 3 | Batas akhir koreksi `13:00` bersifat **inklusif** (`between`), sehingga `13:00:00` masih dapat diedit. | `canEditAbsensi()` | Sedang — ambiguitas boundary | Tentukan apakah batas akhir bersifat inklusif/eksklusif, lalu sesuaikan validasi. |
| 4 | Penutupan sesi dapat dilakukan manual (tombol Tutup) maupun otomatis (cron), dengan logika rekap Alfa yang sama. | `AbsensiController::closeManual` | Rendah — potensi proses ganda bila keduanya berjalan | Pastikan mekanisme idempotent dan pengecekan `is_closed` berjalan sebelum rekap. |

---

*Dokumen pengujian Blackbox ini disusun berdasarkan analisis perilaku implementasi saat
ini. Hasil pengujian pada tabel Matriks (Bagian 6) bersifat indikatif dan perlu
dieksekusi ulang pada lingkungan deploy final sebelum rilis.*
