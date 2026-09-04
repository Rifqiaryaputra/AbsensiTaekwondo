<?php

namespace App\Jobs;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\IzinSakit;
use App\Models\Jadwal;
use App\Services\JadwalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class TutupSesiAbsen implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public readonly int $jadwalId)
    {
    }

    public function handle(JadwalService $jadwalService): void
    {
        $today = Carbon::now()->toDateString();

        $jadwal = Jadwal::find($this->jadwalId);

        if (! $jadwal || $jadwal->is_closed) {
            return;
        }

        if ($jadwalService->isHariLibur(Carbon::now())) {
            return;
        }

        $anggotaIdTanpaKehadiran = Anggota::query()
            ->where('status_anggota', Anggota::STATUS_AKTIF)
            ->whereDoesntHave('absensi', function ($query) use ($jadwal, $today) {
                $query->where('jadwal_id', $jadwal->id)
                    ->whereDate('tanggal', $today);
            })
            ->whereDoesntHave('izinSakit', function ($query) use ($today) {
                $query->whereDate('tanggal', $today)
                    ->where('status', IzinSakit::STATUS_DISETUJUI);
            })
            ->pluck('id');

        $now = Carbon::now();

        foreach ($anggotaIdTanpaKehadiran->chunk(200) as $chunk) {
            $records = $chunk->map(fn ($anggotaId) => [
                'anggota_id' => $anggotaId,
                'jadwal_id' => $jadwal->id,
                'tanggal' => $today,
                'status' => Absensi::STATUS_ALFA,
                'sumber' => Absensi::SUMBER_OTOMATIS,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            Absensi::insertOrIgnore($records);
        }

        $jadwal->update(['is_closed' => true]);
    }
}