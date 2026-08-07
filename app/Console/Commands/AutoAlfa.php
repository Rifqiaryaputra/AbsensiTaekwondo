<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\IzinSakit;
use App\Services\JadwalService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutoAlfa extends Command
{
    protected $signature = 'absen:auto-alfa';

    protected $description = 'Rekap otomatis status Alfa untuk anggota yang tidak hadir dan tidak berizin pada jadwal yang sudah selesai';

    public function handle(JadwalService $jadwalService): int
    {
        $now = Carbon::now();

        if ($jadwalService->isHariLibur($now)) {
            $this->info('Hari ini merupakan hari libur, auto-alfa dilewati.');

            return self::SUCCESS;
        }

        $jadwalSelesai = $jadwalService->getJadwalSelesaiHariIni($now);

        if ($jadwalSelesai->isEmpty()) {
            $this->info('Belum ada jadwal hari ini yang sesi absensinya selesai.');

            return self::SUCCESS;
        }

        $totalAlfa = 0;

        foreach ($jadwalSelesai as $jadwal) {
            $anggotaIdTanpaKehadiran = Anggota::query()
                ->where('status_anggota', Anggota::STATUS_AKTIF)
                ->whereDoesntHave('absensi', function ($query) use ($jadwal, $now) {
                    $query->where('jadwal_id', $jadwal->id)
                        ->whereDate('tanggal', $now->toDateString());
                })
                ->whereDoesntHave('izinSakit', function ($query) use ($now) {
                    $query->whereDate('tanggal', $now->toDateString())
                        ->where('status', IzinSakit::STATUS_DISETUJUI);
                })
                ->pluck('id');

            foreach ($anggotaIdTanpaKehadiran->chunk(500) as $chunk) {
                $records = $chunk->map(fn ($anggotaId) => [
                    'anggota_id' => $anggotaId,
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $now->toDateString(),
                    'status' => Absensi::STATUS_ALFA,
                    'sumber' => Absensi::SUMBER_OTOMATIS,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                Absensi::insert($records);
                $totalAlfa += count($records);
            }
        }

        $this->info("Auto-alfa selesai: {$totalAlfa} anggota ditandai Alfa.");

        return self::SUCCESS;
    }
}
