---
name: Sistem Absensi UKM Taekwondo Kampus
description: Sistem manajemen kehadiran internal berbasis QR Code untuk anggota UKM Taekwondo
colors:
  primary: "#3554D1"
  primary-hover: "#2841A8"
  primary-light: "#EEF2FF"
  background: "#F4F7FE"
  surface: "#FFFFFF"
  text-primary: "#111827"
  text-secondary: "#6B7280"
  text-muted: "#9CA3AF"
  border: "#E5E7EB"
  hadir-bg: "#EDFCF4"
  hadir-text: "#16A34A"
  izin-bg: "#EFF6FF"
  izin-text: "#3B82F6"
  sakit-bg: "#FEFCE8"
  sakit-text: "#EAB308"
  alfa-bg: "#FEF2F2"
  alfa-text: "#EF4444"
typography:
  h1:
    fontFamily: "Poppins, sans-serif"
    fontSize: 1.875rem
    fontWeight: 800
    lineHeight: 1.2
    letterSpacing: -0.01em
  h2:
    fontFamily: "Poppins, sans-serif"
    fontSize: 1.125rem
    fontWeight: 700
    lineHeight: 1.3
  body-md:
    fontFamily: "Inter, sans-serif"
    fontSize: 0.875rem
    fontWeight: 400
    lineHeight: 1.5
  label-sm:
    fontFamily: "Inter, sans-serif"
    fontSize: 0.6875rem
    fontWeight: 700
    letterSpacing: 0.05em
    textTransform: uppercase
rounded:
  sm: 8px
  md: 12px
  lg: 16px
  xl: 24px
  full: 9999px
spacing:
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  2xl: 48px
shadow:
  card: "0 4px 20px -4px rgba(0,0,0,0.03)"
  soft: "0 10px 40px -10px rgba(0,0,0,0.05)"
  colored: "0 8px 20px -4px rgba(53,84,209,0.3)"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
    rounded: "{rounded.full}"
    padding: "10px 24px"
    shadow: "{shadow.colored}"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
  button-secondary:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text-secondary}"
    borderColor: "{colors.border}"
    rounded: "{rounded.full}"
    padding: "10px 24px"
  button-soft:
    backgroundColor: "{colors.primary-light}"
    textColor: "{colors.primary}"
    rounded: "{rounded.full}"
    padding: "10px 24px"
  card:
    backgroundColor: "{colors.surface}"
    rounded: "{rounded.xl}"
    padding: "{spacing.lg}"
    shadow: "{shadow.card}"
  sidebar-item-active:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
    rounded: "{rounded.lg}"
    shadow: "{shadow.colored}"
  sidebar-item:
    textColor: "{colors.text-secondary}"
    hoverBackgroundColor: "{colors.primary-light}"
    hoverTextColor: "{colors.primary}"
    rounded: "{rounded.lg}"
  input:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text-primary}"
    borderColor: "{colors.border}"
    rounded: "{rounded.lg}"
    padding: "12px 16px"
    focusRing: "{colors.primary-light}"
  badge-status:
    rounded: "{rounded.full}"
    padding: "4px 12px"
    fontWeight: 700
  avatar:
    rounded: "{rounded.full}"
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
---

## Overview
Desain bergaya **Soft UI / Tech Modern** untuk sistem administrasi UKM Taekwondo. Layout menggunakan sidebar tetap di kiri (fixed, lebar 280px) dengan konten utama berbackground abu kebiruan lembut (#F4F7FE) dan kartu-kartu putih bersih mengambang di atasnya menggunakan shadow yang sangat halus (tanpa border tegas, tanpa heavy shadow). Aksen warna utama adalah biru indigo (#3554D1) yang dipakai konsisten untuk CTA, item sidebar aktif, ring fokus input, dan highlight angka penting.

## Colors
- **Primary (#3554D1)** dipakai untuk tombol utama, item navigasi aktif, ikon aksen, dan shadow berwarna (colored shadow) pada elemen penting.
- **Primary Light (#EEF2FF)** dipakai sebagai background lembut untuk ikon KPI, badge, hover state, dan focus ring input.
- **Background (#F4F7FE)** adalah warna dasar halaman, sedikit lebih abu-kebiruan dibanding putih murni agar card putih menonjol.
- **Status warna** wajib konsisten di seluruh aplikasi: Hadir (hijau/success), Izin (biru/info), Sakit (kuning/warning), Alfa (merah/error) — masing-masing punya varian background pastel + teks solid (mis. `bg-green-50 text-green-600`).
- Teks menggunakan skala abu-abu: `text-gray-900` untuk judul/nilai penting, `text-gray-500` untuk deskripsi, `text-gray-400` untuk label/meta kecil.

## Typography
- **Poppins** dipakai khusus untuk heading (h1, h2, angka statistik besar, judul kartu) — memberi kesan tegas namun tetap ramah (rounded, friendly geometric sans).
- **Inter** dipakai untuk seluruh body text, label, tabel, dan elemen UI lain — legible di ukuran kecil.
- Label kategori (mis. "TOTAL ANGGOTA", header tabel) selalu uppercase, huruf kecil (10–11px), bold, letter-spacing lebar, warna abu muda.

## Spacing & Layout
- Sidebar fixed kiri lebar 280px, konten utama diberi `margin-left` setara lebar sidebar dengan max-width kontainer 1280px, center-aligned.
- Grid KPI: 2 kolom di mobile, 4 kolom di desktop (`lg:grid-cols-4`), gap 12–24px tergantung breakpoint.
- Card selalu diberi padding besar (24–32px) dan jarak antar section (space-y) 24–32px.
- Border-radius sangat besar dan konsisten: card besar `rounded-3xl` (24px), tombol & elemen kecil `rounded-2xl`/`rounded-full` — kesan lembut/pill di seluruh UI.
- Responsif: sidebar berubah jadi drawer (slide-in dari kiri + overlay blur) di layar <768px, digantikan mobile top header dengan tombol hamburger.

## Components
- **Cards** selalu putih, shadow sangat tipis (`shadow-card`), tanpa border tebal (kadang border tipis `border-gray-50` saja), radius besar (`rounded-3xl`), dan hover state halus (`hover:-translate-y-1` atau `hover:scale-105`) untuk memberi interaktivitas ringan.
- **Tombol primary** berbentuk pill penuh (`rounded-full`) dengan shadow berwarna sesuai warna tombol (mis. `shadow-brand-blue/30`), tombol secondary berupa outline putih dengan border abu tipis.
- **Sidebar item aktif** memakai background primary solid + shadow berwarna + rounded-2xl, sedangkan item non-aktif polos abu dengan hover ke `primary-light`.
- **Badge/status pill** selalu rounded-full, kombinasi background pastel + teks solid warna senada, sering disertai ikon kecil (mis. `local_fire_department`, `warning`).
- **Avatar inisial** berbentuk lingkaran penuh, background warna solid (bisa bervariasi per user: biru, ungu, sky, dsb.) dengan teks putih bold, kadang disertai shadow berwarna senada.
- **Input & select** memakai border abu tipis, radius besar (`rounded-2xl`), dan saat fokus menampilkan ring lembut warna `primary-light` (`focus:ring-4 focus:ring-brand-light`) tanpa outline browser default.
- **Tabel data** memakai header abu sangat muda (`bg-gray-50/80`), label header kecil-uppercase-abu, baris dipisah `divide-y` tipis (bukan border tiap sel), dan hover baris memakai tint primary-light transparan.
- Ikon menggunakan **Material Symbols Outlined** (filled variant, `FILL 1`) secara konsisten di sidebar, tombol, dan status.

## Rules to Never Break
- Jangan gunakan *heavy shadows*, border tebal, atau elemen 3D — semua shadow harus tipis dan lembut (soft UI).
- Jangan gunakan sudut tajam; radius minimum untuk elemen interaktif adalah `rounded-xl`/12px, card besar minimal `rounded-2xl`/16px ke atas.
- Warna status (Hadir/Izin/Sakit/Alfa) wajib konsisten di semua halaman — jangan menukar makna warna.
- Tombol primary selalu memakai warna brand blue + shadow berwarna, jangan gunakan warna acak untuk CTA utama.
- Heading selalu Poppins, body/UI selalu Inter — jangan dicampur terbalik.
- Aksesibilitas warna wajib memenuhi standar WCAG AA (kontras teks abu di atas putih/pastel tetap terbaca jelas).
