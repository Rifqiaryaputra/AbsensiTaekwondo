<?php

namespace App\Services;

use App\Models\HariLibur;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class JadwalService
{
    /**
     * Semua perbandingan waktu memakai zona waktu ini secara eksplisit,
     * tidak bergantung pada timezone default PHP, MySQL, atau server hosting.
     */
    public const TIMEZONE = 'Asia/Jakarta';

    /**
     * Waktu sekarang dalam zona waktu Asia/Jakarta.
     */
    public function waktuSekarang(Carbon|string|null $now = null): Carbon
    {
        if ($now instanceof Carbon) {
            return $now->copy()->setTimezone(self::TIMEZONE);
        }

        return Carbon::now(self::TIMEZONE);
    }

    /**
     * Nama hari dalam Bahasa Indonesia untuk sebuah tanggal.
     */
    public function hariNama(Carbon|string $date): string
    {
        Carbon::setLocale('id');

        $tanggal = $date instanceof Carbon
            ? $date->copy()->setTimezone(self::TIMEZONE)
            : Carbon::parse($date, self::TIMEZONE);

        return $tanggal->translatedFormat('l');
    }

    /**
     * Rentang jam jadwal pada tanggal dari waktu yang diberikan.
     * Perbandingan dilakukan di PHP dengan Carbon, bukan fungsi waktu MySQL.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function rentangJam(Jadwal $jadwal, Carbon $now): array
    {
        $tanggal = $this->waktuSekarang($now)->toDateString();

        $mulai = Carbon::parse($tanggal.' '.$jadwal->jam_start, self::TIMEZONE);
        $selesai = Carbon::parse($tanggal.' '.$jadwal->jam_close, self::TIMEZONE);

        return [$mulai, $selesai];
    }

    /**
     * Jadwal yang sedang aktif pada waktu tertentu (hari sesuai & jam dalam rentang start-close).
     * Rentang bersifat inklusif: tepat pada jam_start dan jam_close masih dianggap aktif.
     */
    public function getActiveJadwal(?Carbon $now = null): ?Jadwal
    {
        $now = $this->waktuSekarang($now);

        return Jadwal::query()
            ->where('hari', $this->hariNama($now))
            ->where('is_closed', false)
            ->orderBy('jam_start')
            ->get()
            ->first(function (Jadwal $jadwal) use ($now) {
                [$mulai, $selesai] = $this->rentangJam($jadwal, $now);

                return $now->betweenIncluded($mulai, $selesai);
            });
    }

    /**
     * Jadwal yang berlaku pada sebuah tanggal (berdasarkan hari), tanpa cek jam.
     */
    public function getJadwalUntukTanggal(Carbon|string $date): ?Jadwal
    {
        $tanggal = $date instanceof Carbon
            ? $date->copy()->setTimezone(self::TIMEZONE)
            : Carbon::parse($date, self::TIMEZONE);

        return Jadwal::query()
            ->where('hari', $this->hariNama($tanggal))
            ->orderBy('jam_start')
            ->first();
    }

    /**
     * Cek apakah sebuah tanggal terdaftar sebagai hari libur.
     */
    public function isHariLibur(Carbon|string $date): bool
    {
        $tanggal = $date instanceof Carbon
            ? $date->copy()->setTimezone(self::TIMEZONE)
            : Carbon::parse($date, self::TIMEZONE);

        return HariLibur::query()
            ->whereDate('tanggal', $tanggal->toDateString())
            ->exists();
    }

    /**
     * Jadwal hari ini yang sesi absensinya sudah selesai (jam_close < waktu sekarang).
     */
    public function getJadwalSelesaiHariIni(?Carbon $now = null): Collection
    {
        $now = $this->waktuSekarang($now);

        return Jadwal::query()
            ->where('hari', $this->hariNama($now))
            ->orderBy('jam_start')
            ->get()
            ->filter(function (Jadwal $jadwal) use ($now) {
                [, $selesai] = $this->rentangJam($jadwal, $now);

                return $now->greaterThan($selesai);
            })
            ->values();
    }

    /**
     * Pesan penolakan saat absen berada di luar jam jadwal hari ini.
     * Menyertakan jam server (WIB) untuk transparansi ke petugas.
     */
    public function pesanDiLuarJam(?Carbon $now = null): string
    {
        $now = $this->waktuSekarang($now);
        $jamSistem = $now->format('H:i:s').' WIB';
        $jadwal = $this->getJadwalUntukTanggal($now);

        if (! $jadwal) {
            return "Bukan jam absen. Tidak ada jadwal latihan hari ini (Jam sistem: {$jamSistem}).";
        }

        [$mulai, $selesai] = $this->rentangJam($jadwal, $now);
        $infoJadwal = "Jadwal: {$mulai->format('H:i')} - {$selesai->format('H:i')} WIB";

        if ($jadwal->is_closed) {
            return "Bukan jam absen. Sesi latihan hari ini sudah ditutup. {$infoJadwal} (Jam sistem: {$jamSistem}).";
        }

        return "Bukan jam absen. {$infoJadwal} (Jam sistem: {$jamSistem}).";
    }

    /**
     * Batas waktu pengajuan izin/sakit: maksimal 2 jam sebelum jam start jadwal pada tanggal terkait.
     */
    public function batasPengajuan(Jadwal $jadwal, Carbon|string $date): Carbon
    {
        return Carbon::parse($date, self::TIMEZONE)->setTimeFromTimeString($jadwal->jam_start)->subHours(2);
    }
}
