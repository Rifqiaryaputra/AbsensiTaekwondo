# PRODUK REQUIREMENTS DOCUMENT (PRD)
# Sistem Absensi UKM Taekwondo Kampus

**STATUS: DRAFT SEMENTARA**

| | |
|---|---|
| **Nama Produk** | Sistem Absensi UKM Taekwondo Kampus |
| **Versi Dokumen** | v0.3 (Draft Sementara) |
| **Disusun oleh** | Tim Pengembang |
| **Untuk** | Pengurus Unit Kegiatan Mahasiswa (UKM) Taekwondo |
| **Tanggal** | 31 Juli 2026 |
| **Dokumen Terkait** | Ringkasan Kebutuhan dari Klien (permintaan fitur langsung), Update Kebutuhan v0.2, `design.md`, Mockup Pengaturan Profil & Tambah Anggota v0.3 |

---

## 1. Ringkasan Produk (Overview)

Saat ini pencatatan kehadiran latihan anggota UKM Taekwondo kampus dilakukan secara manual, kemungkinan besar menggunakan buku absen fisik atau catatan yang tersebar. Proses ini rawan kesalahan pencatatan, sulit direkap untuk kebutuhan laporan bulanan/periodik, dan tidak memberikan gambaran cepat mengenai tingkat keaktifan anggota maupun anggota yang sering tidak hadir (alfa). Selain itu, proses pengajuan izin/sakit anggota yang biasanya dilakukan secara informal (chat/lisan) menyulitkan Petugas untuk mencocokkan status kehadiran secara akurat.

Solusi yang akan dibangun adalah **sistem absensi berbasis web** dengan tiga peran pengguna (Admin, Petugas, dan Anggota), di mana fitur inti sistem adalah **absensi melalui pemindaian (scan) QR Code unik milik masing-masing anggota** oleh Petugas, dilengkapi dengan **halaman Perizinan** untuk persetujuan izin/sakit yang otomatis memperbarui status kehadiran, serta **mekanisme absensi otomatis** yang merekap status Alfa apabila anggota tidak hadir dan tidak mengajukan izin/sakit. Akun login Anggota dan Petugas dibuat otomatis oleh sistem dari data anggota, sehingga tidak perlu proses registrasi terpisah. Sistem ini mencakup manajemen data anggota, manajemen petugas absensi, manajemen jadwal latihan, manajemen hari libur, manajemen perizinan, dashboard ringkasan statistik (yang disesuaikan per peran), serta rekap kehadiran yang dapat difilter berdasarkan rentang tanggal dan diekspor. Tujuan besarnya adalah mendigitalkan dan memusatkan proses absensi UKM Taekwondo agar lebih cepat, akurat, transparan bagi anggota, dan mudah dipantau oleh pengurus.

## 2. Tujuan & Sasaran (Goals)

- Memusatkan pencatatan kehadiran anggota UKM Taekwondo ke dalam satu sistem digital berbasis QR Code.
- Mengurangi kesalahan pencatatan dan duplikasi data absensi yang biasa terjadi pada pencatatan manual.
- Menyediakan data ringkasan (dashboard) secara real-time mengenai jumlah anggota, petugas, kehadiran harian, dan statistik anggota — termasuk ringkasan pribadi bagi Anggota.
- Memberikan transparansi mengenai tingkat keaktifan anggota (anggota teraktif dan anggota sering alfa).
- Menyederhanakan proses pengajuan dan persetujuan izin/sakit anggota, serta menghilangkan input ganda oleh Petugas saat izin disetujui.
- Memastikan status kehadiran tetap terekap secara otomatis (Alfa) meski anggota tidak discan dan tidak mengajukan izin/sakit.
- Mempercepat proses rekap kehadiran periodik melalui fitur filter tanggal dan export data.
- Membatasi akses fitur sistem sesuai peran (Admin, Petugas, Anggota) agar pengelolaan data lebih aman dan terkontrol.

## 3. Pengguna & Peran (Users & Roles)

- **Admin :** Memiliki akses penuh ke seluruh modul sistem, yaitu Dashboard, Absen, Data Anggota, Petugas Absensi, Jadwal, Hari Libur, Perizinan, Rekap Kehadiran, dan Settings. Admin bertanggung jawab atas pengelolaan data master (anggota, petugas, jadwal, hari libur) dan konfigurasi sistem.
- **Petugas :** Memiliki akses ke modul Dashboard, Absen, Data Anggota, Hari Libur, Perizinan, dan Rekap Kehadiran. Petugas bertugas menjalankan proses absensi harian dengan memindai QR Code anggota, dapat melihat/mengelola data anggota serta hari libur sesuai kebutuhan operasional harian, dan memproses (approve/reject) pengajuan izin/sakit anggota.
- **Anggota :** Peran baru dengan akses terbatas hanya pada Dashboard (melihat QR Code pribadi, statistik kehadiran pribadi, serta mengajukan/membatalkan izin atau sakit) dan Settings (mengubah password akun sendiri).

## 4. Ruang Lingkup (Scope)

### 4.1 Termasuk (MVP)

- Autentikasi login untuk peran Admin, Petugas, dan Anggota.
- Pembuatan akun login otomatis untuk Anggota (dan Petugas) saat data anggota didaftarkan oleh Admin.
- Dashboard statistik ringkasan yang disesuaikan per peran (Admin/Petugas: statistik keseluruhan; Anggota: QR Code pribadi & statistik kehadiran pribadi).
- Manajemen (CRUD) data anggota lengkap dengan ID Anggota unik, QR Code unik, dan foto dobok.
- Manajemen (CRUD) petugas absensi berbasis pencarian data anggota yang sudah ada.
- Manajemen (CRUD) jadwal latihan beserta jam mulai dan jam tutup absen.
- Manajemen (CRUD) hari libur.
- Proses absensi oleh Petugas melalui pemindaian QR Code anggota.
- Absensi otomatis (auto-rekap status Alfa) untuk anggota yang tidak discan dan tidak memiliki izin/sakit disetujui saat sesi jadwal ditutup.
- Halaman **Perizinan**: anggota mengajukan izin/sakit, dapat membatalkan pengajuan selama belum disetujui, serta Admin/Petugas menyetujui atau menolak pengajuan; persetujuan otomatis memperbarui status kehadiran anggota pada tanggal terkait.
- Halaman rekap kehadiran dengan filter rentang tanggal dan export data ke Excel.
- Anggota dapat mengubah password akun sendiri melalui menu Settings.

### 4.2 Di Luar Lingkup Awal / Fase Lanjutan

Belum ada fitur eksplisit yang ditunda dari hasil diskusi. Isi menu **Settings** untuk Admin sudah dikonfirmasi berupa **Pengaturan Profil** (identitas organisasi untuk kop surat laporan) — lihat Bab 6.10. Lampiran bukti sakit pada pengajuan izin belum dijabarkan lebih lanjut oleh klien; lihat Bab 14 (Pertanyaan Terbuka / TBD).

## 5. Asumsi & Batasan (Assumptions & Constraints)

- **Asumsi:** Email login Anggota/Petugas dibuat otomatis dengan format `{nim}@webmail.uad.ac.id`, dan password default awal adalah NIM anggota tersebut (disimpan dalam bentuk ter-hash). Anggota dapat mengganti password ini sendiri melalui menu Settings, atau dibantu oleh Admin melalui menu Data Anggota/Petugas.
- **Asumsi:** ID Anggota dibentuk otomatis oleh sistem dengan format `TKD{2 digit awal NIM}-{nomor urut keanggotaan 3 digit}`, contoh `TKD22-001`. Nomor urut (`001`, `002`, dst.) diasumsikan berjalan secara global (bukan per angkatan) kecuali dinyatakan lain oleh klien — lihat Bab 14.
- **Asumsi:** Batas waktu pengajuan izin/sakit adalah **maksimal 2 jam sebelum jam start absen** pada jadwal yang bersangkutan; pengajuan yang diajukan kurang dari 2 jam sebelum jam start absen akan ditolak sistem secara otomatis (tidak dapat diajukan).
- **Asumsi:** "Jika sudah acc hilang" diinterpretasikan sebagai: tombol/opsi pembatalan pengajuan izin **hanya tersedia selama status pengajuan masih "Menunggu"**. Begitu Admin/Petugas menyetujui (acc) pengajuan tersebut, opsi pembatalan oleh anggota otomatis hilang/dinonaktifkan karena status kehadiran sudah terekap final sebagai Izin/Sakit.
- **Asumsi:** "Absen otomatis terekap" diinterpretasikan sebagai proses sistem (dijalankan melalui scheduler/cron Laravel) yang berjalan setelah jam close suatu jadwal: anggota yang belum memiliki catatan kehadiran (belum discan dan tidak memiliki pengajuan izin/sakit yang disetujui) akan otomatis diberi status **Alfa** pada tanggal tersebut.
- **Asumsi:** Anggota tidak memiliki akses ke modul Absen, Data Anggota (milik anggota lain), Petugas Absensi, Jadwal, Hari Libur, Perizinan (selain pengajuan miliknya sendiri), maupun Rekap Kehadiran (selain rekapnya sendiri yang ditampilkan di Dashboard).
- **Asumsi:** Karena fitur rekap kehadiran diminta export ke **Excel** sedangkan stack teknis yang disebutkan adalah DomPDF (khusus PDF), diasumsikan sistem akan menambahkan pustaka export Excel (misalnya `maatwebsite/excel`) sebagai bagian dari stack, sementara DomPDF tetap digunakan untuk kebutuhan export PDF lain (misalnya cetak kartu anggota/QR atau laporan format PDF) jika dibutuhkan di kemudian hari.
- **Asumsi:** QR Code unik anggota digenerate otomatis oleh sistem saat data anggota dibuat, berbasis ID Anggota (misalnya `TKD22-001`), menggunakan pustaka QR Code Generator pada sisi Laravel.
- **Asumsi:** Satu anggota hanya dapat memiliki satu status kehadiran per sesi jadwal per hari (Hadir/Izin/Sakit/Alfa).
- **Asumsi:** Gaya visual & desain akhir mengacu pada file `design.md` yang telah diterima dari klien (gaya Soft UI / Tech Modern) — lihat Bab 8 untuk detail.
- **Asumsi:** Field **No. BPJS** pada data anggota bersifat opsional dan murni disimpan sebagai data pelengkap administrasi; belum ada fitur/laporan spesifik yang memanfaatkan field ini pada MVP kecuali dikonfirmasi lebih lanjut oleh klien (lihat Bab 14).
- **Asumsi:** Halaman **Pengaturan Profil** (Settings) bersifat khusus untuk peran **Admin**, menyimpan data organisasi secara global/singleton (satu set data untuk seluruh sistem, bukan per periode kepengurusan), dan digunakan sebagai kop surat pada laporan absensi yang dicetak/diekspor.
- **Batasan:** Sistem berbasis web (bukan aplikasi mobile native), diakses melalui browser oleh Admin, Petugas, dan Anggota.
- **Batasan:** Tidak ada mekanisme registrasi mandiri untuk peran apa pun; seluruh akun (Anggota, Petugas, Admin) dibuat oleh Admin.

## 6. Kebutuhan Fungsional (Functional Requirements)

### 6.1 Semua Peran — Autentikasi (AUTH)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| AUTH-1 | Sistem menyediakan halaman login dengan email dan password untuk Admin, Petugas, dan Anggota | Wajib |
| AUTH-2 | Sistem mengarahkan pengguna ke menu sesuai hak akses perannya (Admin/Petugas/Anggota) setelah login berhasil | Wajib |
| AUTH-3 | Sistem menyediakan fitur logout | Wajib |
| AUTH-4 | Sistem membatasi akses menu Petugas Absensi dan Jadwal hanya untuk peran Admin | Wajib |
| AUTH-5 | Sistem membatasi akses Anggota hanya pada menu Dashboard dan Settings | Wajib |
| AUTH-6 | Sistem menggunakan Laravel Breeze sebagai scaffolding autentikasi (login, logout, reset/ubah password) | Wajib |

### 6.2 Admin/Petugas — Dashboard (DASH)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| DASH-1 | Sistem menampilkan jumlah total anggota terdaftar | Wajib |
| DASH-2 | Sistem menampilkan jumlah total petugas terdaftar | Wajib |
| DASH-3 | Sistem menampilkan jumlah total hari libur yang terdaftar | Wajib |
| DASH-4 | Sistem menampilkan ringkasan kehadiran hari ini dengan rincian jumlah Hadir, Izin, Sakit, dan Alfa | Wajib |
| DASH-5 | Sistem menampilkan daftar/ringkasan anggota teraktif (berdasarkan frekuensi kehadiran) | Wajib |
| DASH-6 | Sistem menampilkan daftar/ringkasan anggota yang sering Alfa pada bulan berjalan | Wajib |
| DASH-7 | Sistem menampilkan statistik jenis kelamin anggota dalam bentuk Pie Chart | Wajib |

### 6.3 Anggota — Dashboard (DASH-ANG)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| DASH-ANG-1 | Sistem menampilkan QR Code milik anggota yang sedang login pada halaman Dashboard | Wajib |
| DASH-ANG-2 | Sistem menampilkan statistik kehadiran pribadi anggota (rekap Hadir/Izin/Sakit/Alfa) pada halaman Dashboard | Wajib |
| DASH-ANG-3 | Anggota dapat mengajukan izin/sakit langsung dari halaman Dashboard | Wajib |
| DASH-ANG-4 | Anggota dapat melihat status pengajuan izin/sakit miliknya (Menunggu/Disetujui/Ditolak/Dibatalkan) pada halaman Dashboard | Wajib |
| DASH-ANG-5 | Anggota dapat membatalkan pengajuan izin/sakit miliknya selama status masih "Menunggu" | Wajib |

### 6.4 Admin — Data Anggota (ANG)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| ANG-1 | Admin/Petugas dapat melihat daftar seluruh anggota | Wajib |
| ANG-2 | Admin dapat menambahkan data anggota baru dengan field: Nama Lengkap, NIM, Tanggal Lahir, Jenis Kelamin, No. WhatsApp, Fakultas, Program Studi, No. BPJS (opsional), dan Foto Berseragam Dobok | Wajib |
| ANG-3 | Sistem menghasilkan ID Anggota otomatis dengan format `TKD{2 digit awal NIM}-{nomor urut 3 digit}` (contoh: `TKD22-001`) saat anggota baru disimpan | Wajib |
| ANG-4 | Sistem menggenerate QR Code unik secara otomatis untuk setiap anggota baru berbasis ID Anggota | Wajib |
| ANG-5 | Sistem otomatis membuat akun login (role Anggota) saat data anggota baru disimpan, dengan email `{nim}@webmail.uad.ac.id` dan password default berupa NIM anggota (ter-hash) | Wajib |
| ANG-6 | Admin dapat mengubah data anggota yang sudah terdaftar | Wajib |
| ANG-7 | Admin dapat membantu mereset/mengubah password akun anggota | Penting |
| ANG-8 | Admin dapat menghapus data anggota | Penting |
| ANG-9 | Admin/Petugas dapat mencari dan memfilter data anggota (misalnya berdasarkan nama, NIM, ID Anggota, fakultas, atau program studi) | Penting |
| ANG-10 | Admin/Petugas dapat melihat/mengunduh QR Code anggota untuk keperluan cetak kartu identitas | Penting |

### 6.5 Admin — Petugas Absensi (PTG)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| PTG-1 | Admin dapat mencari data anggota (berdasarkan nama/NIM/ID Anggota) untuk dijadikan Petugas | Wajib |
| PTG-2 | Sistem mengubah role akun anggota terkait menjadi Petugas (akun login yang sudah ada dari proses ANG-5 digunakan kembali, tanpa membuat akun baru) | Wajib |
| PTG-3 | Admin dapat melihat daftar seluruh Petugas yang terdaftar | Wajib |
| PTG-4 | Admin dapat mengubah data akun Petugas (email/password) | Wajib |
| PTG-5 | Admin dapat menghapus/menonaktifkan akun Petugas (mengembalikan role menjadi Anggota) | Penting |

### 6.6 Admin — Hari Libur (LIBUR)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| LIBUR-1 | Admin dapat menambahkan data hari libur dengan field Tanggal dan Keterangan | Wajib |
| LIBUR-2 | Admin dapat mengubah data hari libur | Wajib |
| LIBUR-3 | Admin dapat menghapus data hari libur | Wajib |
| LIBUR-4 | Admin/Petugas dapat melihat daftar hari libur | Wajib |
| LIBUR-5 | Sistem tidak membuka sesi absensi pada tanggal yang terdaftar sebagai hari libur | Penting |

### 6.7 Admin — Jadwal (JADWAL)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| JADWAL-1 | Admin dapat menambahkan jadwal latihan dengan menentukan hari (dapat lebih dari satu hari dalam seminggu) | Wajib |
| JADWAL-2 | Admin menentukan jam mulai absen (jam start) dan jam tutup absen (jam close) untuk setiap jadwal | Wajib |
| JADWAL-3 | Admin dapat mengubah dan menghapus data jadwal | Wajib |
| JADWAL-4 | Sistem mengizinkan koreksi status kehadiran pada suatu sesi jadwal sampai dengan pukul 21.00 di hari berikutnya setelah tanggal jadwal berlangsung, setelah itu data terkunci | Penting |

### 6.8 Admin/Petugas — Absensi (ABSEN)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| ABSEN-1 | Petugas dapat membuka halaman Absen dan memindai (scan) QR Code unik anggota menggunakan kamera perangkat | Wajib |
| ABSEN-2 | Sistem mencocokkan hasil scan QR Code dengan data anggota dan mencatat kehadiran secara otomatis sesuai jadwal yang sedang berlangsung | Wajib |
| ABSEN-3 | Petugas dapat menetapkan/mengubah status kehadiran anggota secara manual: Hadir, Izin, Sakit, atau Alfa | Wajib |
| ABSEN-4 | Sistem menolak/menandai proses absen apabila dilakukan di luar rentang jam start–jam close pada jadwal yang berlaku, kecuali dalam masa koreksi (JADWAL-4) | Penting |
| ABSEN-5 | Sistem mencegah pencatatan absen ganda untuk anggota yang sama pada sesi jadwal yang sama | Wajib |
| ABSEN-6 | Sistem secara otomatis menandai status **Alfa** untuk anggota yang belum memiliki catatan kehadiran (tidak discan dan tidak memiliki izin/sakit disetujui) saat jam close jadwal terlewati | Wajib |
| ABSEN-7 | Sistem otomatis memperbarui status kehadiran anggota menjadi "Izin" atau "Sakit" pada tanggal terkait ketika pengajuan Perizinan disetujui, tanpa perlu diinput manual oleh Petugas | Wajib |
| ABSEN-8 | Admin dapat melihat dan mengoreksi seluruh data absensi | Wajib |

### 6.9 Admin/Petugas — Perizinan (IZIN)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| IZIN-1 | Anggota dapat mengajukan izin/sakit melalui Dashboard dengan mengisi tanggal, jenis (Izin/Sakit), dan keterangan/alasan | Wajib |
| IZIN-2 | Sistem menolak pengajuan izin/sakit yang diajukan kurang dari 2 jam sebelum jam start absen pada jadwal terkait | Wajib |
| IZIN-3 | Anggota dapat membatalkan pengajuan izin/sakit miliknya selama status pengajuan masih "Menunggu"; opsi pembatalan otomatis hilang setelah pengajuan disetujui | Wajib |
| IZIN-4 | Admin dan Petugas dapat melihat daftar pengajuan izin/sakit yang masuk pada halaman **Perizinan** | Wajib |
| IZIN-5 | Admin/Petugas dapat menyetujui (approve) atau menolak (reject) pengajuan izin/sakit | Wajib |
| IZIN-6 | Ketika pengajuan disetujui, sistem otomatis memperbarui/membuat catatan status kehadiran anggota pada tanggal yang diajukan menjadi "Izin" atau "Sakit" sesuai jenis pengajuan | Wajib |
| IZIN-7 | Admin/Petugas dapat memfilter daftar pengajuan berdasarkan status (Menunggu/Disetujui/Ditolak/Dibatalkan) | Penting |

### 6.10 Admin/Petugas — Rekap Kehadiran (REKAP)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| REKAP-1 | Admin/Petugas dapat memfilter data rekap kehadiran berdasarkan rentang tanggal awal dan tanggal akhir | Wajib |
| REKAP-2 | Sistem menampilkan hasil rekap kehadiran dalam bentuk tabel (nama anggota, tanggal, status kehadiran, sumber pencatatan) | Wajib |
| REKAP-3 | Admin/Petugas dapat mengekspor hasil rekap kehadiran ke dalam file Excel | Wajib |

### 6.11 Admin — Settings / Pengaturan Profil (SET)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| SET-1 | Admin dapat mengakses menu Settings berupa halaman **Pengaturan Profil** untuk mengelola identitas organisasi | Wajib |
| SET-2 | Admin dapat mengunggah/mengubah Logo Unit Kegiatan (tampil di sisi kiri kop surat) dan Logo Universitas (tampil di sisi kanan kop surat), masing-masing format PNG/JPG maks. 2MB | Wajib |
| SET-3 | Admin dapat mengisi/mengubah Nama Unit Kegiatan, Nama Universitas, dan Alamat/Sekretariat | Wajib |
| SET-4 | Data pada Pengaturan Profil (logo, nama unit, nama universitas, alamat) ditampilkan sebagai kop surat pada dokumen laporan absensi yang dicetak/diekspor (mis. export PDF) | Penting |
| SET-5 | Admin dapat menyimpan perubahan Pengaturan Profil melalui tombol "Simpan Perubahan" | Wajib |

**Catatan:** Berdasarkan Bab 3, menu Settings tidak termasuk dalam daftar akses Petugas (Petugas hanya memiliki akses ke Dashboard, Absen, Data Anggota, Hari Libur, Perizinan, dan Rekap Kehadiran), sehingga Pengaturan Profil diasumsikan **khusus Admin**. Perlu konfirmasi klien apabila Petugas juga membutuhkan akses ubah password melalui menu Settings — lihat Bab 14.

### 6.12 Anggota — Settings (SET-ANG)

| ID | Kebutuhan Fungsional | Prioritas |
|---|---|---|
| SET-ANG-1 | Anggota dapat mengubah password akun miliknya sendiri melalui menu Settings | Wajib |

## 7. Alur Pengguna Utama (Key User Flows)

### 7.1 Alur Absensi oleh Petugas (Happy Path)

1. Petugas login ke sistem menggunakan email dan password.
2. Petugas membuka menu **Absen**.
3. Sistem menampilkan jadwal latihan yang sedang aktif (dalam rentang jam start–jam close).
4. Petugas memindai QR Code milik anggota menggunakan kamera.
5. Sistem mencocokkan QR Code dengan data anggota dan mencatat status kehadiran anggota sebagai "Hadir".
6. Sistem menampilkan notifikasi konfirmasi bahwa absensi anggota berhasil dicatat.
7. Proses berulang untuk anggota berikutnya hingga sesi absen ditutup.

### 7.2 Alur Anggota Mengajukan Izin/Sakit

1. Anggota login ke sistem dan membuka halaman **Dashboard**.
2. Anggota memilih opsi "Ajukan Izin/Sakit" dan mengisi tanggal, jenis (Izin/Sakit), serta keterangan.
3. Sistem memeriksa apakah waktu pengajuan masih memenuhi batas minimal 2 jam sebelum jam start absen pada jadwal terkait.
4. Jika memenuhi syarat, sistem menyimpan pengajuan dengan status "Menunggu"; jika tidak, sistem menolak dan menampilkan pesan kesalahan.
5. Anggota dapat melihat status pengajuannya pada Dashboard.

### 7.3 Alur Pembatalan Pengajuan Izin/Sakit

1. Anggota membuka Dashboard dan melihat daftar pengajuan izin/sakit miliknya.
2. Anggota memilih pengajuan berstatus "Menunggu" dan menekan tombol "Batalkan".
3. Sistem mengubah status pengajuan menjadi "Dibatalkan".
4. Jika pengajuan sudah berstatus "Disetujui", tombol "Batalkan" tidak lagi ditampilkan/tidak dapat digunakan.

### 7.4 Alur Persetujuan Perizinan oleh Admin/Petugas

1. Admin/Petugas membuka halaman **Perizinan**.
2. Sistem menampilkan daftar pengajuan izin/sakit dengan status "Menunggu".
3. Admin/Petugas meninjau detail pengajuan (nama anggota, tanggal, jenis, keterangan).
4. Admin/Petugas menekan tombol "Setujui" atau "Tolak".
5. Jika disetujui, sistem otomatis membuat/memperbarui catatan absensi anggota pada tanggal terkait menjadi status "Izin" atau "Sakit" sesuai jenis pengajuan, tanpa perlu diinput ulang secara manual.
6. Jika ditolak, status pengajuan berubah menjadi "Ditolak" dan status kehadiran anggota tetap mengikuti mekanisme normal (scan/absen otomatis).

### 7.5 Alur Absensi Otomatis (Auto-Alfa)

1. Sistem menjalankan proses terjadwal (scheduler) setelah jam close suatu sesi jadwal terlewati.
2. Untuk setiap anggota yang belum memiliki catatan kehadiran pada sesi tersebut (tidak discan dan tidak memiliki pengajuan izin/sakit yang disetujui), sistem otomatis membuat catatan kehadiran dengan status "Alfa".
3. Data ini kemudian tersedia untuk dikoreksi oleh Petugas/Admin selama masih dalam masa koreksi (hingga pukul 21.00 keesokan harinya).

### 7.6 Alur Admin Menambahkan Anggota Baru (dengan Auto-Provisioning Akun)

1. Admin login ke sistem.
2. Admin membuka menu **Data Anggota** dan memilih "Tambah Anggota".
3. Admin mengisi Nama Lengkap, NIM, Tanggal Lahir, Jenis Kelamin, No. WhatsApp, Fakultas, Program Studi, No. BPJS (opsional), dan mengunggah Foto Berseragam Dobok (PNG/JPG, maks. 2MB).
4. Sistem menghasilkan ID Anggota otomatis (format `TKD22-001`) dan QR Code unik berbasis ID tersebut.
5. Sistem otomatis membuat akun login untuk anggota dengan email `{nim}@webmail.uad.ac.id` dan password default berupa NIM.
6. Sistem menampilkan konfirmasi data anggota berhasil ditambahkan beserta pratinjau QR Code dan informasi akun login yang dibuat.

### 7.7 Alur Admin Menetapkan Anggota Menjadi Petugas

1. Admin membuka menu **Petugas Absensi** dan memilih "Tambah Petugas".
2. Admin mencari anggota berdasarkan nama, NIM, atau ID Anggota dari data anggota yang sudah ada.
3. Admin memilih anggota yang dituju; sistem mengubah role akun login anggota tersebut (yang sudah otomatis dibuat sebelumnya) menjadi Petugas.
4. Anggota yang bersangkutan kini dapat login sebagai Petugas menggunakan akun yang sama.

### 7.8 Alur Rekap dan Export Kehadiran

1. Admin/Petugas membuka menu **Rekap Kehadiran**.
2. Pengguna memilih tanggal awal dan tanggal akhir sebagai filter.
3. Sistem menampilkan tabel rekap kehadiran sesuai rentang tanggal yang dipilih.
4. Pengguna menekan tombol "Export ke Excel".
5. Sistem menghasilkan file Excel berisi data rekap kehadiran sesuai filter dan mengunduhnya ke perangkat pengguna.

### 7.9 Alur Admin Mengatur Profil Unit Kegiatan (Pengaturan Profil)

1. Admin login ke sistem dan membuka menu **Settings**.
2. Sistem menampilkan halaman **Pengaturan Profil** berisi form Logo Unit Kegiatan, Logo Universitas, Nama Unit Kegiatan, Nama Universitas, dan Alamat/Sekretariat (terisi data sebelumnya jika sudah pernah diatur).
3. Admin mengunggah/mengganti logo dan/atau mengubah data teks yang tersedia.
4. Admin menekan tombol "Simpan Perubahan".
5. Sistem menyimpan data profil organisasi dan menggunakannya sebagai kop surat pada dokumen laporan absensi yang dicetak/diekspor selanjutnya.

## 8. UI/UX Overview

### 8.1 Gaya & Tema Visual

File referensi **`design.md`** telah diterima dari klien. Gaya desain yang ditetapkan adalah **Soft UI / Tech Modern**:

- **Layout:** Sidebar tetap (fixed) di kiri selebar 280px, konten utama berbackground abu-kebiruan lembut (`#F4F7FE`) dengan kartu-kartu putih mengambang menggunakan shadow yang sangat halus (tanpa border tegas, tanpa heavy shadow/3D). Max-width kontainer konten 1280px, center-aligned.
- **Warna Aksen Utama:** Biru indigo `#3554D1` (dengan hover `#2841A8` dan varian soft `#EEF2FF`), dipakai konsisten untuk tombol CTA, item sidebar aktif, focus ring input, dan shadow berwarna pada elemen penting.
- **Warna Status Kehadiran** (wajib konsisten di seluruh aplikasi, tidak boleh tertukar maknanya):
  - Hadir: hijau (`bg #EDFCF4` / teks `#16A34A`)
  - Izin: biru (`bg #EFF6FF` / teks `#3B82F6`)
  - Sakit: kuning (`bg #FEFCE8` / teks `#EAB308`)
  - Alfa: merah (`bg #FEF2F2` / teks `#EF4444`)
- **Tipografi:** Poppins (bold, 700–800) untuk heading, judul kartu, dan angka statistik besar; Inter (400) untuk seluruh body text, label, dan tabel. Label kategori/header tabel selalu uppercase, kecil (10–11px), bold, letter-spacing lebar, warna abu muda.
- **Radius & Bentuk:** Sudut tajam tidak diperbolehkan — radius minimum elemen interaktif `rounded-xl`/12px, card besar minimal `rounded-2xl`–`rounded-3xl` (16–24px); tombol umumnya pill penuh (`rounded-full`).
- **Komponen kunci:** Tombol primary pill biru + shadow berwarna; badge status pill dengan background pastel + teks solid senada warna status; avatar inisial bulat dengan background solid; tabel dengan header abu sangat muda, baris dipisah `divide-y` tipis (bukan border per sel), hover baris tint `primary-light`; input & select radius besar dengan focus ring lembut warna primary-light.
- **Ikon:** Material Symbols Outlined (variant filled, `FILL 1`) digunakan konsisten di sidebar, tombol, dan status.
- **Aksesibilitas:** Kontras warna teks abu di atas putih/pastel wajib memenuhi standar WCAG AA.

Detail lengkap palet warna, skala tipografi, spacing, shadow, dan spesifikasi tiap komponen tersedia pada file `design.md` (sumber acuan utama saat implementasi UI/Tailwind config).

### 8.2 Perangkat & Responsivitas

- Sistem bersifat web responsif, dapat diakses melalui desktop maupun perangkat mobile/tablet. Halaman Absen (Petugas) dan Dashboard (Anggota, untuk melihat QR Code) diprioritaskan optimal di perangkat mobile karena kemungkinan besar diakses langsung di lokasi latihan.
- Sesuai `design.md`: pada layar <768px, sidebar berubah menjadi drawer (slide-in dari kiri + overlay blur), digantikan mobile top header dengan tombol hamburger.
- Grid KPI pada Dashboard: 2 kolom di mobile, 4 kolom di desktop (`lg:grid-cols-4`).

### 8.3 Referensi Desain

Referensi desain mengacu pada file `design.md` (diterima 31 Juli 2026) dan dua mockup awal yang disusulkan klien: halaman **Pengaturan Profil** (Settings Admin) dan modal **Tambah Anggota Baru**, keduanya konsisten dengan gaya Soft UI / Tech Modern pada `design.md` (card putih rounded besar, input rounded dengan label uppercase abu, tombol primary pill biru dengan shadow berwarna). Belum ada referensi kompetitor/produk lain yang disebutkan.

### 8.4 Wireframe / Mockup Level Tinggi

Dua mockup awal telah diterima dari klien dan menjadi acuan pola desain untuk halaman/komponen serupa lainnya:
- **Pengaturan Profil** (Admin — Settings): form dua kolom untuk upload logo (Unit Kegiatan & Universitas), input Nama Unit Kegiatan, Nama Universitas, dan textarea Alamat/Sekretariat, dengan catatan info bahwa alamat akan tampil di kop surat laporan.
- **Tambah Anggota Baru** (Admin — Data Anggota): modal form dua kolom berisi Nama Lengkap, NIM, Tanggal Lahir, Jenis Kelamin, No. WhatsApp, Fakultas, Program Studi, No. BPJS (opsional), dan upload Foto Berseragam Dobok.

Wireframe untuk halaman lain (Dashboard, Absen/Scan QR, Jadwal, Hari Libur, Perizinan, Rekap Kehadiran) belum tersedia; akan disusun pada tahap desain UI/UX dengan mengikuti pola visual pada dua mockup di atas dan `design.md`, mengacu pada struktur menu final per peran:
- **Admin:** Dashboard, Absen, Data Anggota, Petugas Absensi, Jadwal, Hari Libur, Perizinan, Rekap Kehadiran, Settings.
- **Petugas:** Dashboard, Absen, Data Anggota, Hari Libur, Perizinan, Rekap Kehadiran.
- **Anggota:** Dashboard (QR Code pribadi, statistik pribadi, pengajuan izin/sakit), Settings (ubah password).

## 9. Model Data & Database Overview (High-Level)

### 9.1 Ringkasan Basis Data

Sistem akan menggunakan basis data **relasional** (disarankan MySQL/MariaDB, selaras dengan ekosistem Laravel) mengingat relasi antar entitas cukup terstruktur dan membutuhkan integritas referensial yang kuat. Satu **Anggota** memiliki satu **akun login (users)** yang dapat memiliki role Anggota atau Petugas (di-upgrade oleh Admin tanpa membuat akun baru); satu **Anggota** dapat memiliki banyak **Data Absensi** dan banyak **Pengajuan Izin/Sakit**; satu **Jadwal** dapat memiliki banyak **Data Absensi**; dan setiap **Pengajuan Izin/Sakit** yang disetujui akan menghasilkan/memperbarui satu **Data Absensi** terkait. Pemilihan jenis database relasional ini merupakan asumsi pengembang (lihat Bab 5) karena tidak disebutkan eksplisit oleh klien.

### 9.2 Detail Entitas

| Entitas | Field Utama | Keterangan |
|---|---|---|
| **users** | id, name, email, password, role (admin/petugas/anggota), anggota_id (FK, nullable untuk admin), force_password_change, created_at, updated_at | Akun login terpusat untuk seluruh peran; email & password default anggota di-generate otomatis (lihat Bab 5) |
| **anggota** | id, id_anggota (format `TKD22-001`), nama_lengkap, nim, tanggal_lahir, jenis_kelamin, no_whatsapp, foto_dobok, fakultas, program_studi, no_bpjs (nullable), qr_code, created_at, updated_at | Data induk anggota UKM; qr_code berisi kode unik hasil generate sistem berbasis id_anggota; no_bpjs bersifat opsional |
| **hari_libur** | id, tanggal, keterangan, created_at, updated_at | Daftar tanggal libur latihan |
| **jadwal** | id, hari, jam_start, jam_close, created_at, updated_at | Jadwal latihan mingguan beserta jam buka/tutup absen |
| **absensi** | id, anggota_id (FK), jadwal_id (FK), petugas_id (FK, nullable), izin_sakit_id (FK, nullable), tanggal, status (hadir/izin/sakit/alfa), sumber (scan/manual/otomatis/izin_disetujui), waktu_scan, created_at, updated_at | Catatan kehadiran anggota per sesi jadwal; field sumber menandai asal pencatatan |
| **izin_sakit** | id, anggota_id (FK), jadwal_id (FK), tanggal, jenis (izin/sakit), keterangan, status (menunggu/disetujui/ditolak/dibatalkan), diproses_oleh (FK ke users, nullable), diajukan_pada, diproses_pada, created_at, updated_at | Pengajuan izin/sakit anggota beserta status persetujuannya |
| **pengaturan_profil** | id, logo_unit_kegiatan, logo_universitas, nama_unit_kegiatan, nama_universitas, alamat_sekretariat, created_at, updated_at | Data identitas organisasi (single-row/singleton) untuk kop surat laporan absensi; dikelola melalui menu Settings Admin (lihat Bab 6.11) |

Catatan: seluruh field pada tabel di atas termasuk dalam cakupan MVP; belum ada field khusus Fase Lanjutan yang teridentifikasi.

## 10. Kebutuhan Non-Fungsional (Non-Functional Requirements)

- **Keamanan & Hak Akses :** Sistem wajib menerapkan pembatasan akses berbasis peran (role-based access control) secara konsisten di sisi backend (Admin/Petugas/Anggota), bukan hanya menyembunyikan menu di sisi tampilan.
- **Keamanan Akun Default :** Karena password awal anggota bersifat dapat ditebak (default = NIM), disarankan sistem mendorong/mewajibkan anggota mengganti password saat login pertama kali (flag `force_password_change`).
- **Performa :** Proses scan QR Code hingga pencatatan kehadiran tersimpan harus berlangsung cepat (idealnya di bawah 2 detik) agar tidak menghambat antrean anggota saat absensi berlangsung.
- **Responsivitas :** Tampilan, khususnya halaman Absen dan Dashboard Anggota, harus optimal diakses melalui perangkat mobile.
- **Integritas Data :** Sistem harus mencegah duplikasi pencatatan absensi untuk anggota yang sama pada sesi jadwal yang sama, termasuk saat terjadi tumpang tindih antara scan manual, absensi otomatis, dan persetujuan izin.
- **Keamanan Data Login :** Password akun Admin, Petugas, dan Anggota wajib disimpan dalam bentuk terenkripsi (hashing), sesuai standar keamanan Laravel/Breeze.
- **Auditabilitas :** Setiap perubahan status kehadiran (koreksi manual maupun otomatis dari persetujuan izin) sebaiknya tercatat (field `sumber`, `diproses_oleh`) agar dapat ditelusuri.
- **Keandalan Proses Terjadwal :** Proses absensi otomatis (auto-Alfa) harus berjalan konsisten via scheduler Laravel setiap jadwal ditutup, dengan mekanisme pencegahan proses ganda (idempotent).

## 11. Kebutuhan Teknis (Technical Requirements)

### 11.1 Arsitektur & Platform

- **Arsitektur Web Monolitik berbasis Laravel 12 :** Backend dan rendering tampilan dibangun dalam satu aplikasi Laravel, menggunakan Blade dan komponen **Laravel Livewire** untuk interaktivitas dinamis (misalnya scan QR real-time, update status Perizinan, tabel rekap dengan filter tanpa reload penuh), serta Vite sebagai build tool aset frontend (CSS/JS).
- **Scheduler Laravel** digunakan untuk menjalankan proses absensi otomatis (auto-Alfa) setelah jam close jadwal terlewati.

### 11.2 Teknologi / Stack

| Layer | Teknologi | Catatan |
|---|---|---|
| Backend Framework | Laravel 12 | Menangani logic bisnis, autentikasi, dan role-based access |
| Autentikasi | Laravel Breeze | Scaffolding login, logout, manajemen password |
| Interaktivitas Frontend | Laravel Livewire | Komponen dinamis: scan QR, approval Perizinan, tabel dengan filter real-time |
| Frontend Styling | TailwindCSS | Mendukung implementasi gaya desain mengacu pada `design.md` |
| Build Tool | Vite | Kompilasi dan bundling aset frontend |
| QR Code | QR Code Generator (pustaka Laravel, misal `simplesoftwareio/simple-qrcode`) | Generate QR Code unik per anggota berbasis ID Anggota |
| Export PDF | DomPDF | Untuk kebutuhan export/cetak dokumen berformat PDF |
| Export Excel | Pustaka export Excel (asumsi: `maatwebsite/excel`) | Dibutuhkan untuk fitur export rekap kehadiran ke Excel; belum disebutkan eksplisit oleh klien (lihat Bab 5 & 14) |
| Database | MySQL/MariaDB (asumsi) | Basis data relasional pendukung Laravel |
| Chart | Pustaka chart berbasis JS (misal Chart.js/ApexCharts, akan ditentukan pada tahap desain) | Untuk Pie Chart statistik jenis kelamin & statistik pribadi anggota |
| Scheduler | Laravel Task Scheduling | Menjalankan proses auto-Alfa terjadwal |

### 11.3 Hosting & Environment

Detail hosting dan environment (server, domain, tier layanan) belum dibahas dalam permintaan klien; lihat Bab 14 (Pertanyaan Terbuka / TBD).

## 12. Integrasi Pihak Ketiga

| Layanan | Fungsi | Catatan |
|---|---|---|
| QR Code Generator (library) | Membuat QR Code unik untuk setiap anggota | Termasuk MVP |
| DomPDF | Export data ke format PDF | Termasuk MVP |
| Pustaka Export Excel | Export rekap kehadiran ke format Excel | Termasuk MVP (asumsi pemilihan pustaka, lihat Bab 5) |
| Domain email `webmail.uad.ac.id` | Basis pembentukan email login otomatis anggota | Bukan integrasi aktif (tidak ada pengiriman email/verifikasi ke domain ini pada MVP), hanya digunakan sebagai format string email |

Belum ada permintaan integrasi ke layanan eksternal lain (misalnya integrasi WhatsApp API untuk notifikasi) meskipun field No. WhatsApp dicatat pada data anggota; lihat Bab 14.

## 13. Fitur Usulan / Fase Lanjutan

Belum ada usulan fase lanjutan yang secara eksplisit disampaikan dalam permintaan klien. Beberapa kemungkinan pengembangan lanjutan yang dapat dipertimbangkan ke depan (bukan bagian dari MVP dan belum disepakati):

- **Notifikasi WhatsApp Otomatis.** Memanfaatkan field No. WhatsApp anggota yang sudah tercatat untuk mengirim notifikasi otomatis (misalnya pengingat jadwal latihan, notifikasi hasil approval Perizinan, atau notifikasi status Alfa) melalui integrasi WhatsApp API. Fitur ini berkaitan dengan data anggota dan modul Perizinan pada MVP namun membutuhkan integrasi pihak ketiga tambahan yang belum dibahas.
- **Verifikasi Email Login Anggota.** Mengingat email login dibentuk otomatis dari NIM (bukan email aktif yang diverifikasi anggota), pengiriman email verifikasi/notifikasi sesungguhnya ke domain `webmail.uad.ac.id` dapat dipertimbangkan pada fase lanjutan jika dibutuhkan.

## 14. Pertanyaan Terbuka / TBD

- Apakah nomor urut pada ID Anggota (`TKD22-001`) berjalan **global** (lintas angkatan) atau **per angkatan** (reset setiap 2 digit NIM/tahun berbeda)?
- Apakah pengajuan izin/sakit memerlukan lampiran bukti (misalnya surat sakit/dokter) atau cukup keterangan teks saja?
- Apakah dibutuhkan notifikasi (WhatsApp/email) kepada anggota saat pengajuan izin/sakit disetujui atau ditolak?
- Apakah dibutuhkan integrasi WhatsApp (mengingat field No. WhatsApp anggota dicatat) untuk notifikasi, atau field ini murni untuk keperluan data kontak saja?
- Detail hosting/server dan domain yang akan digunakan untuk deploy sistem.
- Apakah dibutuhkan histori/log perubahan (audit trail) lengkap untuk setiap koreksi status kehadiran (siapa mengubah, kapan, dari status apa ke status apa)?
- Apakah satu anggota bisa memiliki lebih dari satu sesi jadwal dalam satu hari (misalnya latihan pagi dan sore), dan bagaimana pencatatan absensinya jika ya?
- Format/kolom pasti yang perlu ditampilkan pada file export Excel rekap kehadiran (kolom apa saja yang wajib ada).
- Apakah email login otomatis (`{nim}@webmail.uad.ac.id`) perlu divalidasi/diverifikasi kepemilikannya, atau cukup digunakan sebagai identifier login saja?
- **[Baru]** Untuk apa field **No. BPJS (Opsional)** pada data anggota digunakan (mis. keperluan klaim asuransi/cedera saat latihan, syarat administrasi kampus, atau sekadar data pelengkap)? Apakah perlu ditampilkan/dicetak pada laporan tertentu?
- **[Baru]** Apakah menu **Settings/Pengaturan Profil** juga perlu diakses oleh Petugas (mis. untuk ubah password sendiri), atau Petugas belum memiliki menu Settings sama sekali sesuai daftar akses di Bab 3?
- **[Baru]** Apakah Pengaturan Profil bersifat **satu data global** untuk seluruh sistem (single-row), atau bisa berbeda per periode kepengurusan (butuh histori)?

## 15. Glosarium

- **Anggota :** Mahasiswa yang terdaftar sebagai peserta latihan UKM Taekwondo, memiliki ID Anggota dan QR Code unik, serta akun login dengan akses terbatas (Dashboard & Settings).
- **Petugas :** Pengguna sistem (berasal dari data anggota yang di-upgrade rolenya oleh Admin) yang menjalankan proses absensi harian dan memproses Perizinan.
- **ID Anggota :** Kode identitas unik anggota dengan format `TKD{2 digit awal NIM}-{nomor urut 3 digit}`, contoh `TKD22-001`.
- **QR Code Unik :** Kode identitas digital yang digenerate otomatis oleh sistem berbasis ID Anggota, digunakan sebagai media scan saat absensi.
- **Dobok :** Seragam latihan Taekwondo.
- **Alfa :** Status kehadiran yang menandakan anggota tidak hadir tanpa keterangan, dapat tercatat otomatis oleh sistem.
- **Perizinan :** Modul/halaman untuk mengelola pengajuan izin/sakit anggota beserta proses persetujuan oleh Admin/Petugas.
- **Masa Koreksi :** Rentang waktu (hingga pukul 21.00 keesokan harinya) di mana status kehadiran suatu sesi jadwal masih dapat diubah sebelum data terkunci.

---

*Dokumen ini merupakan draft sementara dan dapat berubah seiring pembahasan lebih lanjut dengan klien, termasuk setelah file `design.md` diterima.*
