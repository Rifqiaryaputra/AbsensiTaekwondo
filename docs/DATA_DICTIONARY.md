# PERANCANGAN BASIS DATA
## Spesifikasi Tabel (Data Dictionary)
### Sistem Absensi Cerdas Berbasis QR Code — UKM Taekwondo

|                 |                                                                                 |
| --------------- | ------------------------------------------------------------------------------- |
| **Nama Sistem** | Sistem Absensi Cerdas Berbasis QR Code UKM Taekwondo                            |
| **Sistem Basis Data** | MySQL / MariaDB (relasional, mendukung ekosistem Laravel)                  |
| **Framework**   | Laravel 12 (Eloquent ORM + Migration)                                            |
| **Penyusun**    | Database Administrator & System Analyst                                          |
| **Tanggal**     | Agustus 2026                                                                    |

---

## 1. PENDAHULUAN

Bagian ini menyajikan **spesifikasi tabel basis data (data dictionary)** dari sistem,
yang disusun berdasarkan struktur **migration** Laravel yang digunakan saat ini
(16 file migration). Data dictionary berfungsi sebagai rujukan teknis untuk
memastikan setiap kolom, tipe data, batasan (*constraint*), kunci (*key*), dan relasi
antar tabel terdefinisi secara eksplisit dan konsisten.

Basis data sistem dikelompokkan menjadi dua kategori:

1. **Tabel Utama (Domain)** — tabel yang merepresentasikan entitas bisnis inti:
   `users`, `anggota`, `fakultas`, `program_studi`, `jadwal`, `hari_libur`,
   `absensi`, `izin_sakit`, `jadwal_petugas`, dan `pengaturan_profil`.
2. **Tabel Pendukung Framework** — tabel bawaan yang dihasilkan secara otomatis oleh
   Laravel untuk keperluan autentikasi, sesi, cache, dan antrean (queue): `sessions`,
   `password_reset_tokens`, `cache`, `cache_locks`, `jobs`, `job_batches`, dan
   `failed_jobs`.

> **Konvensi tipe data yang digunakan:**
> - `BIGINT UNSIGNED` — id utama (*auto-increment*) & *foreign key*.
> - `VARCHAR` — teks panjang terbatas.
> - `TEXT` / `MEDIUMTEXT` / `LONGTEXT` — teks panjang tak terbatas.
> - `ENUM` — nilai terbatas yang sudah ditetapkan.
> - `DATE`, `TIME`, `TIMESTAMP` — tipe data waktu.
> - `BOOLEAN` — nilai benar/salah (tersimpan sebagai `TINYINT(1)`).
> - Kolom `created_at` dan `updated_at` adalah *timestamp* yang diisi otomatis oleh
>   Eloquent (elemen `$table->timestamps()`).

---

## 2. RINGKASAN RELASI ANTAR TABEL

| Tabel Sumber | Relasi | Tabel Tujuan | Jenis | Kunci Penghubung |
|---|---|---|---|---|
| `users` | 1 : N | `anggota` | *One-to-One* (opsional) | `anggota_id` |
| `anggota` | 1 : N | `absensi` | *One-to-Many* | `anggota_id` |
| `anggota` | 1 : N | `izin_sakit` | *One-to-Many* | `anggota_id` |
| `anggota` | N : 1 | `fakultas` | *Many-to-One* | `fakultas_id` |
| `anggota` | N : 1 | `program_studi` | *Many-to-One* | `program_studi_id` |
| `fakultas` | 1 : N | `program_studi` | *One-to-Many* | `fakultas_id` |
| `jadwal` | 1 : N | `absensi` | *One-to-Many* | `jadwal_id` |
| `jadwal` | 1 : N | `izin_sakit` | *One-to-Many* | `jadwal_id` |
| `users` | 1 : N | `absensi` | *One-to-Many* (opsional) | `petugas_id` |
| `izin_sakit` | 1 : N | `absensi` | *One-to-Many* (opsional) | `izin_sakit_id` |
| `users` | M : N | `jadwal` | *Many-to-Many* via pivot `jadwal_petugas` | `jadwal_id`, `user_id` |

---

## 3. SPESIFIKASI TABEL UTAMA (DOMAIN)

### 3.1 Tabel `users`

**Deskripsi:** Menyimpan akun login (autentikasi) untuk seluruh peran pengguna, yaitu
Admin, Petugas, dan Anggota. Setiap Anggota dapat memiliki satu akun login yang
terhubung ke tabel `anggota` melalui kolom `anggota_id`.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `name` | VARCHAR | NOT NULL | – |
| `email` | VARCHAR | NOT NULL, UNIQUE | – |
| `email_verified_at` | TIMESTAMP | NULL (opsional) | – |
| `password` | VARCHAR | NOT NULL (tersimpan *hash*) | – |
| `anggota_id` | BIGINT UNSIGNED | NULL, FOREIGN KEY → `anggota.id`, ON DELETE CASCADE | – |
| `role` | ENUM('admin','petugas','anggota') | NOT NULL, DEFAULT `'anggota'` | – |
| `force_password_change` | BOOLEAN | NOT NULL, DEFAULT `false` | – |
| `remember_token` | VARCHAR(100) | NULL | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

**Keterangan tambahan:**
- Kolom `role` menentukan hak akses pengguna terhadap menu aplikasi.
- `force_password_change` digunakan untuk memaksa anggota mengganti password default pada saat login pertama kali.
- `anggota_id` bersifat opsional (NULL) karena akun dengan peran Admin tidak selalu terkait dengan data anggota.

---

### 3.2 Tabel `anggota`

**Deskripsi:** Menyimpan data induk (master) seluruh peserta UKM Taekwondo. Tabel ini
merupakan sumber data utama yang dihubungkan dengan catatan absensi, pengajuan
izin/sakit, dan akun login.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `id_anggota` | VARCHAR | NOT NULL, UNIQUE (format `TKD22-001`) | – |
| `nama_lengkap` | VARCHAR | NOT NULL | – |
| `nim` | VARCHAR | NOT NULL | – |
| `tanggal_lahir` | DATE | NOT NULL | – |
| `jenis_kelamin` | ENUM('L','P') | NOT NULL | – |
| `no_whatsapp` | VARCHAR | NULL (opsional) | – |
| `foto_dobok` | VARCHAR | NULL (opsional, menyimpan *path* file) | – |
| `no_bpjs` | VARCHAR | NULL (opsional) | – |
| `status_anggota` | ENUM('aktif','non-aktif','alumni') | NOT NULL, DEFAULT `'aktif'` | – |
| `fakultas_id` | BIGINT UNSIGNED | NULL, FOREIGN KEY → `fakultas.id`, ON DELETE SET NULL | – |
| `program_studi_id` | BIGINT UNSIGNED | NULL, FOREIGN KEY → `program_studi.id`, ON DELETE SET NULL | – |
| `qr_code` | VARCHAR | NOT NULL (menyimpan *path* file QR Code) | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

**Keterangan tambahan:**
- `id_anggota` dihasilkan otomatis oleh sistem dengan format `TKD{2 digit awal NIM}-{nomor urut 3 digit}`.
- `qr_code` berisi *path* gambar QR Code unik yang di-*generate* berbasis `id_anggota`.
- Kolom `fakultas` dan `program_studi` (berupa *string*) versi awal telah digantikan dengan `fakultas_id` dan `program_studi_id` (berupa *foreign key*) untuk normalisasi data.
- `status_anggota` membedakan anggota yang masih aktif, tidak aktif, maupun alumni.

---

### 3.3 Tabel `fakultas`

**Deskripsi:** Menyimpan daftar fakultas pada universitas sebagai data referensi untuk
kategorisasi anggota.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `nama_fakultas` | VARCHAR | NOT NULL, UNIQUE | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

---

### 3.4 Tabel `program_studi`

**Deskripsi:** Menyimpan daftar program studi, masing-masing terhubung ke satu fakultas.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `fakultas_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → `fakultas.id`, ON DELETE CASCADE | – |
| `nama_prodi` | VARCHAR | NOT NULL | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

---

### 3.5 Tabel `jadwal`

**Deskripsi:** Menyimpan jadwal latihan mingguan beserta jam mulai dan jam tutup
absensi. Kolom `is_closed` menandakan apakah sesi suatu jadwal telah ditutup
(data kehadiran terkunci / masa koreksi berakhir).

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `hari` | ENUM('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') | NOT NULL | – |
| `jam_start` | TIME | NOT NULL | – |
| `jam_close` | TIME | NOT NULL | – |
| `is_closed` | BOOLEAN | NOT NULL, DEFAULT `false` | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

**Keterangan tambahan:**
- `jam_start` (jam buka) dan `jam_close` (jam tutup) menjadi acuan validasi waktu proses absensi.
- `is_closed` berfungsi sebagai penanda status terkunci; ketika bernilai `true`, sesi tidak lagi dapat dikoreksi.

---

### 3.6 Tabel `hari_libur`

**Deskripsi:** Menyimpan daftar tanggal libur latihan. Pada tanggal yang terdaftar,
sistem tidak membuka sesi absensi.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `tanggal` | DATE | NOT NULL, UNIQUE | – |
| `keterangan` | VARCHAR | NOT NULL | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

---

### 3.7 Tabel `jadwal_petugas` (Tabel Pivot / *Many-to-Many*)

**Deskripsi:** Tabel penghubung relasi *Many-to-Many* antara `users` (petugas) dan
`jadwal`. Menentukan petugas mana yang bertugas pada jadwal tertentu.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `jadwal_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → `jadwal.id`, ON DELETE CASCADE | – |
| `user_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → `users.id`, ON DELETE CASCADE | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

**Kendala unik:** Kombinasi (`jadwal_id`, `user_id`) bersifat **UNIQUE** — sebuah
petugas tidak dapat ditugaskan dua kali pada jadwal yang sama.

---

### 3.8 Tabel `absensi`

**Deskripsi:** Tabel inti yang menyimpan catatan kehadiran setiap anggota pada setiap
sesi jadwal. Berisi status kehadiran serta sumber pencatatan.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `anggota_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → `anggota.id` | – |
| `jadwal_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → `jadwal.id` | – |
| `petugas_id` | BIGINT UNSIGNED | NULL, FOREIGN KEY → `users.id`, ON DELETE SET NULL | – |
| `izin_sakit_id` | BIGINT UNSIGNED | NULL, FOREIGN KEY → `izin_sakit.id`, ON DELETE SET NULL | – |
| `tanggal` | DATE | NOT NULL | – |
| `status` | ENUM('hadir','izin','sakit','alfa') | NOT NULL | – |
| `sumber` | ENUM('scan','manual','otomatis','izin_disetujui') | NOT NULL | – |
| `waktu_scan` | TIMESTAMP | NULL (opsional) | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

**Kendala unik:** Kombinasi (`anggota_id`, `jadwal_id`, `tanggal`) bersifat
**UNIQUE** — mencegah terjadinya duplikasi catatan kehadiran (absensi ganda).

**Keterangan tambahan:**
- `status` menunjukkan hasil kehadiran: `hadir`, `izin`, `sakit`, atau `alfa`.
- `sumber` menelusuri asal pencatatan: hasil *scan* QR, input manual NIM, rekapitulasi otomatis (cron), atau hasil persetujuan izin.
- `petugas_id` mengidentifikasi petugas yang memproses absensi (NULL bila diproses otomatis).
- `izin_sakit_id` menghubungkan catatan absensi dengan pengajuan izin/sakit yang disetujui (NULL bila tidak berasal dari izin).

---

### 3.9 Tabel `izin_sakit`

**Deskripsi:** Menyimpan data pengajuan izin/sakit yang diajukan oleh anggota serta
status persetujuannya oleh Admin/Petugas.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `anggota_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → `anggota.id` | – |
| `jadwal_id` | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY → `jadwal.id` | – |
| `tanggal` | DATE | NOT NULL | – |
| `jenis` | ENUM('izin','sakit') | NOT NULL | – |
| `keterangan` | TEXT | NOT NULL | – |
| `bukti_lampiran` | VARCHAR | NULL (opsional, menyimpan *path* file bukti) | – |
| `status` | ENUM('menunggu','disetujui','ditolak','dibatalkan') | NOT NULL, DEFAULT `'menunggu'` | – |
| `diproses_oleh` | BIGINT UNSIGNED | NULL, FOREIGN KEY → `users.id`, ON DELETE SET NULL | – |
| `diajukan_pada` | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | – |
| `diproses_pada` | TIMESTAMP | NULL (diisi saat disetujui/ditolak) | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

**Keterangan tambahan:**
- `status` mengikuti siklus: `menunggu` → `disetujui` / `ditolak`, atau `menunggu` → `dibatalkan`.
- `diproses_oleh` mencatat siapa (Admin/Petugas) yang memproses pengajuan.
- Pada saat status menjadi `disetujui`, sistem otomatis membuat/memperbarui catatan pada tabel `absensi`.

---

### 3.10 Tabel `pengaturan_profil`

**Deskripsi:** Tabel *singleton* (satu baris) yang menyimpan identitas organisasi UKM
untuk keperluan kop surat pada dokumen laporan yang dicetak/diekspor.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `logo_unit_kegiatan` | VARCHAR | NULL (opsional, menyimpan *path* file logo) | – |
| `logo_universitas` | VARCHAR | NULL (opsional, menyimpan *path* file logo) | – |
| `nama_unit_kegiatan` | VARCHAR | NULL | – |
| `nama_universitas` | VARCHAR | NULL | – |
| `alamat_sekretariat` | VARCHAR | NULL | – |
| `created_at` | TIMESTAMP | NULL | – |
| `updated_at` | TIMESTAMP | NULL | – |

**Keterangan tambahan:** Seluruh kolom bersifat opsional (NULL) karena tabel ini
diharapkan hanya memiliki satu baris data konfigurasi organisasi.

---

## 4. SPESIFIKASI TABEL PENDUKUNG FRAMEWORK

### 4.1 Tabel `password_reset_tokens`

**Deskripsi:** Menyimpan token untuk fitur reset kata sandi pengguna.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `email` | VARCHAR | NOT NULL | **PRIMARY KEY** |
| `token` | VARCHAR | NOT NULL | – |
| `created_at` | TIMESTAMP | NULL | – |

### 4.2 Tabel `sessions`

**Deskripsi:** Menyimpan data sesi login pengguna (default driver sesi database).

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | VARCHAR | NOT NULL | **PRIMARY KEY** |
| `user_id` | BIGINT UNSIGNED | NULL, INDEX | – |
| `ip_address` | VARCHAR(45) | NULL | – |
| `user_agent` | TEXT | NULL | – |
| `payload` | LONGTEXT | NOT NULL | – |
| `last_activity` | INTEGER | NOT NULL, INDEX | – |

### 4.3 Tabel `cache`

**Deskripsi:** Menyimpan data cache aplikasi.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `key` | VARCHAR | NOT NULL | **PRIMARY KEY** |
| `value` | MEDIUMTEXT | NOT NULL | – |
| `expiration` | INTEGER | NOT NULL, INDEX | – |

### 4.4 Tabel `cache_locks`

**Deskripsi:** Menyimpan data kunci (*lock*) cache untuk mekanisme *mutex*.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `key` | VARCHAR | NOT NULL | **PRIMARY KEY** |
| `owner` | VARCHAR | NOT NULL | – |
| `expiration` | INTEGER | NOT NULL, INDEX | – |

### 4.5 Tabel `jobs`

**Deskripsi:** Menyimpan antrean pekerjaan (*queue jobs*).

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `queue` | VARCHAR | NOT NULL, INDEX | – |
| `payload` | LONGTEXT | NOT NULL | – |
| `attempts` | TINYINT UNSIGNED | NOT NULL | – |
| `reserved_at` | INTEGER UNSIGNED | NULL | – |
| `available_at` | INTEGER UNSIGNED | NOT NULL | – |
| `created_at` | INTEGER UNSIGNED | NOT NULL | – |

### 4.6 Tabel `job_batches`

**Deskripsi:** Menyimpan informasi batch pekerjaan antrean.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | VARCHAR | NOT NULL | **PRIMARY KEY** |
| `name` | VARCHAR | NOT NULL | – |
| `total_jobs` | INTEGER | NOT NULL | – |
| `pending_jobs` | INTEGER | NOT NULL | – |
| `failed_jobs` | INTEGER | NOT NULL | – |
| `failed_job_ids` | LONGTEXT | NOT NULL | – |
| `options` | MEDIUMTEXT | NULL | – |
| `cancelled_at` | INTEGER | NULL | – |
| `created_at` | INTEGER | NOT NULL | – |
| `finished_at` | INTEGER | NULL | – |

### 4.7 Tabel `failed_jobs`

**Deskripsi:** Menyimpan daftar pekerjaan yang gagal diproses.

| Kolom | Tipe Data | Atribut / Kendala | Kunci |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | AUTO_INCREMENT, NOT NULL | **PRIMARY KEY** |
| `uuid` | VARCHAR | NOT NULL, UNIQUE | – |
| `connection` | TEXT | NOT NULL | – |
| `queue` | TEXT | NOT NULL | – |
| `payload` | LONGTEXT | NOT NULL | – |
| `exception` | LONGTEXT | NOT NULL | – |
| `failed_at` | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | – |

---

## 5. STRUKTUR KUNCI DAN INDEKS (RINGKASAN)

| Tabel | Primary Key | Kendala Unik | Indeks / Foreign Key |
|---|---|---|---|
| `users` | `id` | `email` | `anggota_id` → `anggota.id` |
| `anggota` | `id` | `id_anggota` | `fakultas_id` → `fakultas.id`; `program_studi_id` → `program_studi.id` |
| `fakultas` | `id` | `nama_fakultas` | – |
| `program_studi` | `id` | – | `fakultas_id` → `fakultas.id` |
| `jadwal` | `id` | – | – |
| `hari_libur` | `id` | `tanggal` | – |
| `jadwal_petugas` | `id` | (`jadwal_id`, `user_id`) | `jadwal_id` → `jadwal.id`; `user_id` → `users.id` |
| `absensi` | `id` | (`anggota_id`, `jadwal_id`, `tanggal`) | `anggota_id`, `jadwal_id`, `petugas_id`, `izin_sakit_id` |
| `izin_sakit` | `id` | – | `anggota_id`, `jadwal_id`, `diproses_oleh` → `users.id` |
| `pengaturan_profil` | `id` | – | – |

---

*Dokumen ini merupakan perancangan basis data versi 1.0 dan disusun mengikuti struktur
migration yang aktif pada sistem. Perubahan skema di masa mendatang hendaknya
diperbarui melalui file migration baru dan dicerminkan kembali pada dokumen ini agar
tetap sinkron.*
