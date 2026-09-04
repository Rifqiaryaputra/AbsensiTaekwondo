# SPESIFIKASI ACTIVITY DIAGRAM
## Sistem Absensi Cerdas Berbasis QR Code — UKM Taekwondo
### Alur Pelaksanaan Absensi, Koreksi, dan Rekap Otomatis

|                 |                                                                |
| --------------- | -------------------------------------------------------------- |
| **Nama Sistem** | Sistem Absensi Cerdas Berbasis QR Code UKM Taekwondo           |
| **Penyusun**    | System Analyst                                                 |
| **Tanggal**     | Agustus 2026                                                    |

---

## 1. NARASI ALUR

Alur dimulai dari **Petugas/Admin** yang membuka menu "Scan Absensi". Sistem kemudian
melakukan dua validasi: (1) memeriksa hak akses pengguna, dan (2) mencocokkan waktu
saat ini dengan Jadwal Latihan yang berlaku. Hasil pencocokan waktu terbagi menjadi
tiga kemungkinan (percabangan):

1. **Belum Mulai** — Waktu masih di luar jam buka. Sistem menampilkan pesan
   *"Jadwal belum aktif"* dan proses selesai.

2. **Sedang Aktif** — Waktu berada di rentang jam buka–tutup. Sistem mengaktifkan
   antarmuka **Kamera Scanner**. Petugas mengarahkan QR Code anggota ke kamera,
   atau memasukkan NIM secara manual apabila kamera gagal membaca. Sistem kemudian
   memvalidasi data anggota:
   - Jika **valid**, sistem mencatat status **"Hadir"** ke database, lalu **loop**
     kembali ke antarmuka Scanner untuk anggota berikutnya.
   - Jika **tidak valid**, sistem menampilkan pesan QR tidak dikenali dan kembali
     ke antarmuka Scanner untuk pemindaian ulang.

3. **Sudah Lewat / Ditutup** — Waktu melewati jam tutup. Sistem menyembunyikan
   Scanner dan menampilkan tabel **Masa Koreksi**. Petugas dapat mengubah status
   (Sakit / Izin / Alfa / Hadir), lalu sistem menyimpan pembaruan status ke database.

Secara **paralel di latar belakang**, setelah jadwal melewati jam tutup, **Cron Job**
berjalan otomatis: mencari anggota yang belum memiliki catatan kehadiran pada
jadwal tersebut, lalu mengubah status mereka menjadi **"Alfa"** (tanpa intervensi
manual dari Petugas/Admin).

---

## 2. DIAGRAM — KODE MERMAID.JS

> **Catatan:** Mermaid.js **tidak memiliki sintaks swimlane bawaan**. Cara paling
> rapi untuk membuat swimlane adalah memakai `subgraph` sebagai representasi lintasan
> (lane) aktor. Blok berikut dapat dirender langsung di mermaid.live / GitHub / Notion.

````markdown
```mermaid
flowchart TD

    %% ==================== LANE: PETUGAS / ADMIN ====================
    subgraph L_USER["LANE 1 : PETUGAS / ADMIN"]
        direction TB
        Start(("Mulai"))
        P1["Buka menu<br/>\"Scan Absensi\""]
        P2["Arahkan QR Code ke kamera<br/>atau ketik NIM manual"]
        P3["Ubah status anggota<br/>(Sakit / Izin / Alfa / Hadir)"]
        End1(("Selesai"))
    end

    %% ==================== LANE: SISTEM ====================
    subgraph L_SYS["LANE 2 : SISTEM"]
        direction TB
        S1["Cek validasi hak akses &<br/>cocokkan waktu dengan Jadwal"]
        Jam{"Status Waktu"}
        S2["Tampilkan pesan:<br/>\"Jadwal belum aktif\""]
        S3["Aktifkan antarmuka<br/>Kamera Scanner"]
        S4{"Validasi<br/>data anggota?"}
        S5["Catat status \"Hadir\"<br/>ke database"]
        S6["Sembunyikan Scanner,<br/>tampilkan tabel Masa Koreksi"]
        S7["Simpan pembaruan<br/>status ke database"]
        S8["Tampilkan pesan:<br/>QR tidak dikenali"]
    end

    %% ==================== LANE: BACKGROUND / CRON ====================
    subgraph L_CRON["LANE 3 : BACKGROUND / CRON JOB"]
        direction TB
        C1["Cari anggota yang belum<br/>memiliki status kehadiran"]
        C2["Ubah status menjadi<br/>\"Alfa\" secara otomatis"]
        End2(("Selesai"))
    end

    %% ==================== HUBUNGAN ANTAR LANE ====================
    Start --> P1
    P1 --> S1
    S1 --> Jam

    %% --- Percabangan waktu: BELUM MULAI ---
    Jam -->|"Belum Mulai"| S2
    S2 --> End1

    %% --- Percabangan waktu: SEDANG AKTIF ---
    Jam -->|"Sedang Aktif"| S3
    S3 --> P2
    P2 --> S4
    S4 -->|"Valid"| S5
    S5 -->|"Loop: anggota berikutnya"| S3
    S4 -->|"Tidak Valid"| S8
    S8 -->|"Scan ulang"| P2

    %% --- Percabangan waktu: SUDAH LEWAT / DITUTUP ---
    Jam -->|"Sudah Lewat / Ditutup"| S6
    S6 --> P3
    P3 --> S7
    S7 --> End1

    %% --- Paralel: CRON JOB ---
    S6 -->|"setelah jam tutup"| C1
    C1 --> C2
    C2 --> End2

    %% ==================== STYLING ====================
    classDef laneU fill:#EDFCF4,stroke:#16A34A,stroke-width:2px,color:#111827;
    classDef laneS fill:#EEF2FF,stroke:#3554D1,stroke-width:2px,color:#111827;
    classDef laneC fill:#FEFCE8,stroke:#EAB308,stroke-width:2px,color:#111827;
    classDef act fill:#FFFFFF,stroke:#6B7280,stroke-width:1px,color:#111827;
    classDef dec fill:#FFFFFF,stroke:#3554D1,stroke-width:2px,color:#111827,shape:hexagon;
    classDef startend fill:#3554D1,stroke:#2841A8,color:#fff,stroke-width:2px;

    class L_USER laneU;
    class L_SYS laneS;
    class L_CRON laneC;
    class P1,P2,P3,S1,S2,S3,S5,S6,S7,S8,C1,C2 act;
    class Jam,S4 dec;
    class Start,End1,End2 startend;
```
````

### Keterangan Elemen Visual
| Simbol | Makna | Bentuk Mermaid |
|---|---|---|
| **Start / End** | Titik mulai & akhir alur | Bulat penuh / stadium (`(("..."))`) |
| **Aksi** | Aktivitas yang dilakukan aktor | Kotak (`["..."]`) |
| **Decision** | Percabangan kondisi (Waktu, Validasi) | Belah ketupat / hexagon (`{"..."}`) |
| **Loop** | Perulangan kembali ke Scanner | Panah kembali (`S5 --> S3`) |
| **Paralel** | Proses latar belakang Cron Job | Lane terpisah `L_CRON` |

---

## 3. DIAGRAM — KODE PLANTUML (SWIMLANE ASLI)

PlantUML mendukung swimlane sebenarnya via sintaks `|NamaLane|`. Gunakan jika ingin
garis lintasan (lane) vertikal yang jelas.

````markdown
```plantuml
@startuml
title Alur Pelaksanaan Absensi, Koreksi, dan Rekap Otomatis

|#EDFCF4|Petugas / Admin|
start
:Buka menu "Scan Absensi";

|#EEF2FF|Sistem|
:Cek validasi hak akses &
cocokkan waktu dengan Jadwal;

if (Status Waktu?) then (Belum Mulai)
  :Tampilkan pesan "Jadwal belum aktif";
  stop
elseif (Sedang Aktif) then
  :Aktifkan antarmuka Kamera Scanner;
  |#EDFCF4|Petugas / Admin|
  repeat
    :Arahkan QR Code ke kamera
    atau ketik NIM manual;
    |#EEF2FF|Sistem|
    if (Validasi data anggota?) then (Valid)
      :Catat status "Hadir" ke database;
    else (Tidak Valid)
      :Tampilkan pesan "QR tidak dikenali";
    endif
  repeat while (Ada anggota lagi?) is (Ya)
  -> Stop;

elseif (Sudah Lewat / Ditutup) then
  :Sembunyikan Scanner,
  tampilkan tabel Masa Koreksi;
  |#EDFCF4|Petugas / Admin|
  :Ubah status anggota
  (Sakit / Izin / Alfa / Hadir);
  |#EEF2FF|Sistem|
  :Simpan pembaruan status ke database;
  stop
endif

|#FEFCE8|Background / Cron Job|
note "Dijalankan otomatis setelah jam tutup"
:Cari anggota yang belum memiliki status kehadiran;
:Ubah status menjadi "Alfa" otomatis;
stop

@enduml
```
````

---

*Dokumen ini merupakan spesifikasi Activity Diagram versi 1.0. Silakan sesuaikan bila terdapat perubahan pada logika percabangan waktu atau proses latar belakang.*
