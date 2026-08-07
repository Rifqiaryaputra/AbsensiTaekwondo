<?php

namespace App\Services;

use App\Models\HariLibur;
use App\Models\Jadwal;
use Carbon\Carbon;

class JadwalService
{
    /**
     * Nama hari dalam Bahasa Indonesia untuk sebuah tanggal.
     */
    public function hariNama(Carbon|string $date): string
    {
        Carbon::setLocale('id');

        return Carbon::parse($date)->translatedFormat('l');
    }

    /**
     * Jadwal yang sedang aktif pada waktu tertentu (hari sesuai & jam dalam rentang start-close).
     */
    public function getActiveJadwal(Carbon $now = null): ?Jadwal
    {
        $now ??= Carbon::now();

        return Jadwal::query()
            ->where('hari', $this->hariNama($now))
            ->whereTime('jam_start', '<=', $now->toTimeString())
            ->whereTime('jam_close', '>=', $now->toTimeString())
            ->where('is_closed', false)
            ->first();
    }

    /**
     * Jadwal yang berlaku pada sebuah tanggal (berdasarkan hari), tanpa cek jam.
     */
    public function getJadwalUntukTanggal(Carbon|string $date): ?Jadwal
    {
        return Jadwal::query()
            ->where('hari', $this->hariNama($date))
            ->first();
    }

    /**
     * Cek apakah sebuah tanggal terdaftar sebagai hari libur.
     */
    public function isHariLibur(Carbon|string $date): bool
    {
        return HariLibur::query()
            ->whereDate('tanggal', Carbon::parse($date)->toDateString())
            ->exists();
    }

    /**
     * Jadwal hari ini yang sesi absensinya sudah selesai (jam_close < waktu sekarang).
     */
    public function getJadwalSelesaiHariIni(Carbon $now = null): \Illuminate\Database\Eloquent\Collection
    {
        $now ??= Carbon::now();

        return Jadwal::query()
            ->where('hari', $this->hariNama($now))
            ->whereTime('jam_close', '<', $now->toTimeString())
            ->get();
    }

    /**
     * Batas waktu pengajuan izin/sakit: maksimal 2 jam sebelum jam start jadwal pada tanggal terkait.
     */
    public function batasPengajuan(Jadwal $jadwal, Carbon|string $date): Carbon
    {
        return Carbon::parse($date)->setTimeFromTimeString($jadwal->jam_start)->subHours(2);
    }
}
