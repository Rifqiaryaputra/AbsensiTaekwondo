# SPESIFIKASI USE CASE DIAGRAM
## Sistem Absensi Cerdas Berbasis QR Code — UKM Taekwondo

|                 |                                                                |
| --------------- | -------------------------------------------------------------- |
| **Nama Sistem** | Sistem Absensi Cerdas Berbasis QR Code UKM Taekwondo           |
| **Penyusun**    | System Analyst                                                 |
| **Tanggal**     | Agustus 2026                                                    |

---

## 1. PENJELASAN RELASI AKTOR & USE CASE

### 1.1 Aktor
| Aktor | Tipe | Deskripsi |
|---|---|---|
| **Admin** | Primary (Super User) | Pengguna dengan hak akses penuh. Memiliki seluruh kemampuan Petugas (Relasi **Generalization**) ditambah kemampuan mengelola data master dan melihat laporan. |
| **Petugas** | Primary | Pengurus yang menjalankan presensi. Akses dibatasi hanya pada jadwal yang ditugaskan kepadanya. |
| **Anggota** | Secondary (Pasif) | Tidak login ke sistem. Berperan sebagai subjek yang **memiliki QR Code** untuk di-scan oleh Petugas. |
| **Sistem / Cron Job** | System (Otomatis) | Aktor otomatis berjalan di latar belakang (background process) tanpa interaksi manusia. |

### 1.2 Relasi Antar Aktor
- **Generalization (Admin → Petugas):** Admin **mewarisi** seluruh kemampuan Petugas. Artinya Admin dapat melakukan "Scan QR Absensi" dan "Koreksi Absen" di semua jadwal, sama seperti Petugas, namun tanpa batasan jadwal. Pada diagram, relasi ini digambarkan dari Petugas (anak) ke Admin (induk) dengan stereotip `<<generalization>>`.
- **Asosiasi (Association):** Garis yang menghubungkan aktor dengan use case yang boleh diaksesnya.

### 1.3 Relasi Antar Use Case
| Relasi | Keterangan |
|---|---|
| **<<include>>** | Login. Setiap aktivitas utama Admin & Petugas **wajib** meng-include use case "Melakukan Login". Artinya pemanggilan aktivitas tidak bisa berjalan tanpa proses login terlebih dahulu. |
| **<<extend>>** | Menginput NIM Manual. Merupakan perluasan opsional dari "Melakukan Scan QR Absensi". Use case ini hanya dijalankan **jika** pemindaian kamera QR gagal. |
| **Generalization** | Admin mewarisi kemampuan Petugas (scan & koreksi). |

### 1.4 Daftar Use Case & Pemetaan Aktor
| Use Case | Aktor | Jenis |
|---|---|---|
| Melakukan Login | Admin, Petugas | Include (wajib bagi semua aktivitas utama) |
| Mengelola Data Anggota | Admin | Tugas inti Admin |
| Mencetak QR Code Anggota | Admin | Tugas inti Admin |
| Mengelola Jadwal & Penugasan Petugas | Admin | Tugas inti Admin |
| Melihat Laporan Kehadiran | Admin | Tugas inti Admin |
| Melakukan Scan QR Absensi | Admin, Petugas | Tugas inti |
| Menginput NIM Manual | Petugas (Admin) | Extend dari Scan QR |
| Melakukan Koreksi Absen | Admin, Petugas | Tugas inti |
| Menjalankan Auto-Alfa (Rekap Otomatis) | Sistem / Cron Job | Tugas otomatis |

---

## 2. DIAGRAM — KODE MERMAID.JS

Blok berikut dapat dirender langsung menggunakan editor Mermaid (mermaid.live, GitHub, Notion, MkDocs, dll).

````markdown
```mermaid
flowchart TD
    %% ==================== AKTOR ====================
    Admin([Admin <br/> Super User])
    Petugas([Petugas])
    Anggota([Anggota <br/> Pasif])
    Cron([Sistem / Cron Job])

    %% ==================== SYSTEM BOUNDARY ====================
    subgraph SUR["Sistem Absensi QR Code UKM Taekwondo"]
        direction TB
        %% --- Use Case ---
        UC_Login[Melakukan Login]
        UC_Anggota[Mengelola Data Anggota]
        UC_Print[Mencetak QR Code Anggota]
        UC_Jadwal[Mengelola Jadwal & Penugasan Petugas]
        UC_Laporan[Melihat Laporan Kehadiran]
        UC_Scan[Melakukan Scan QR Absensi]
        UC_Manual[Menginput NIM Manual]
        UC_Koreksi[Melakukan Koreksi Absen]
        UC_Alfa[Menjalankan Auto-Alfa <br/> Rekap Otomatis]
    end

    %% ==================== ASOSIASI AKTOR - USE CASE ====================
    Admin --> UC_Anggota
    Admin --> UC_Print
    Admin --> UC_Jadwal
    Admin --> UC_Laporan
    Admin --> UC_Scan
    Admin --> UC_Koreksi

    Petugas --> UC_Scan
    Petugas --> UC_Koreksi

    Cron --> UC_Alfa

    %% ==================== INCLUDE: LOGIN ====================
    UC_Anggota -.->|include| UC_Login
    UC_Print -.->|include| UC_Login
    UC_Jadwal -.->|include| UC_Login
    UC_Laporan -.->|include| UC_Login
    UC_Scan -.->|include| UC_Login
    UC_Koreksi -.->|include| UC_Login

    %% ==================== EXTEND: NIM MANUAL ====================
    UC_Manual -.->|extend| UC_Scan

    %% ==================== GENERALIZATION ====================
    Anggota -.->|memiliki QR Code| UC_Scan
    Petugas -.->|Generalization| Admin

    %% ==================== STYLING ====================
    classDef actor fill:#3554D1,stroke:#2841A8,color:#fff,stroke-width:2px;
    classDef uc fill:#EEF2FF,stroke:#3554D1,color:#111827,stroke-width:1px;
    classDef sistem fill:#F4F7FE,stroke:#E5E7EB,color:#111827;

    class Admin,Petugas,Anggota,Cron actor;
    class UC_Login,UC_Anggota,UC_Print,UC_Jadwal,UC_Laporan,UC_Scan,UC_Manual,UC_Koreksi,UC_Alfa uc;
    class SUR sistem;
```
````

### Catatan Visual
- **Aktor** digambar dengan bentuk kapsul (stadium) berwarna biru indigo `#3554D1`.
- **Use Case** digambar dengan kotak (rectangle) berwarna krem lembut.
- **System Boundary** direpresentasikan dengan subgraph berlabel "Sistem Absensi QR Code UKM Taekwondo" — seluruh use case berada di dalamnya, sedangkan aktor berada di luar.
- **Line putus-putus** mewakili relasi stereo (include / extend / generalization); **line solid** mewakili asosiasi biasa.

---

## 3. DIAGRAM — KODE PLANTUML (ALTERNATIF)

Jika menggunakan PlantUML, gunakan kode berikut dengan sintaks `plantuml`:

````markdown
```plantuml
@startuml
left to right direction
skinparam packageStyle rectangle

actor "Admin\n(Super User)" as Admin
actor "Petugas" as Petugas
actor "Anggota (Pasif)" as Anggota
actor "Sistem / Cron Job" as Cron

rectangle "Sistem Absensi QR Code UKM Taekwondo" {
    usecase "Melakukan Login" as Login
    usecase "Mengelola Data Anggota" as UC_Anggota
    usecase "Mencetak QR Code Anggota" as UC_Print
    usecase "Mengelola Jadwal &\nPenugasan Petugas" as UC_Jadwal
    usecase "Melihat Laporan Kehadiran" as UC_Laporan
    usecase "Melakukan Scan QR Absensi" as UC_Scan
    usecase "Menginput NIM Manual" as UC_Manual
    usecase "Melakukan Koreksi Absen" as UC_Koreksi
    usecase "Menjalankan Auto-Alfa\n(Rekap Otomatis)" as UC_Alfa
}

' --- Asosiasi ---
Admin --> UC_Anggota
Admin --> UC_Print
Admin --> UC_Jadwal
Admin --> UC_Laporan
Admin --> UC_Scan
Admin --> UC_Koreksi

Petugas --> UC_Scan
Petugas --> UC_Koreksi

Cron --> UC_Alfa

' --- Include: Login ---
UC_Anggota ..> Login : <<include>>
UC_Print ..> Login : <<include>>
UC_Jadwal ..> Login : <<include>>
UC_Laporan ..> Login : <<include>>
UC_Scan ..> Login : <<include>>
UC_Koreksi ..> Login : <<include>>

' --- Extend ---
UC_Manual .> UC_Scan : <<extend>>

' --- Generalization ---
Anggota .> UC_Scan : memiliki QR Code
Petugas <|-- Admin : <<generalization>>
@enduml
```
````

> **Catatan Generalization:** Pada baris `Petugas <|-- Admin`, tanda panah kosong mengarah dari Admin (anak) ke Petugas (induk) sebagai penanda relasi pewarisan.

---

*Dokumen ini merupakan spesifikasi Use Case Diagram versi 1.0. Silakan sesuaikan jika terdapat perubahan pada hak akses aktor atau penambahan fitur.*
