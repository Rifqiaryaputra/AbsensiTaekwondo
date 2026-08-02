<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\IzinSakit;
use Carbon\Carbon;

class AbsensiService
{
    public function __construct(protected JadwalService $jadwalService)
    {
    }

    /**
     * Catat kehadiran anggota dengan validasi:
     *  V1: jadwal aktif (hari & rentang jam_start-jam_close),
     *  V2: bukan hari libur,
     *  V3: tidak ada absensi ganda pada sesi yang sama.
     *
     * @return array{ok: bool, message: string, absensi: ?Absensi}
     */
    public function catatKehadiran(Anggota $anggota, string $sumber, ?int $petugasId, Carbon $now = null): array
    {
        $now ??= Carbon::now();
        $jadwal = $this->jadwalService->getActiveJadwal($now);

        if (! $jadwal) {
            return ['ok' => false, 'message' => 'Tidak ada jadwal latihan yang aktif saat ini. Absen hanya dibuka pada jam jadwal berlangsung.', 'absensi' => null];
        }

        if ($this->jadwalService->isHariLibur($now)) {
            return ['ok' => false, 'message' => 'Hari ini merupakan hari libur, absensi tidak dibuka.', 'absensi' => null];
        }

        $existing = Absensi::query()
            ->where('anggota_id', $anggota->id)
            ->where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', $now->toDateString())
            ->first();

        if ($existing) {
            return ['ok' => false, 'message' => "{$anggota->nama_lengkap} sudah tercatat ({$existing->status}) pada sesi ini.", 'absensi' => $existing];
        }

        $absensi = Absensi::create([
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'petugas_id' => $petugasId,
            'tanggal' => $now->toDateString(),
            'status' => Absensi::STATUS_HADIR,
            'sumber' => $sumber,
            'waktu_scan' => $now,
        ]);

        return ['ok' => true, 'message' => "Absensi {$anggota->nama_lengkap} berhasil dicatat sebagai Hadir.", 'absensi' => $absensi];
    }

    /**
     * Koreksi manual status kehadiran (ABSEN-3 / ABSEN-8).
     */
    public function perbaruiStatus(Absensi $absensi, string $status, ?int $petugasId): Absensi
    {
        $absensi->status = $status;
        $absensi->sumber = Absensi::SUMBER_MANUAL;
        $absensi->petugas_id = $petugasId ?: $absensi->petugas_id;
        $absensi->save();

        return $absensi;
    }

    /**
     * Catat/perbarui absensi otomatis dari pengajuan izin yang disetujui (ABSEN-7).
     */
    public function recordIzinDisetujui(IzinSakit $izin): Absensi
    {
        $absensi = Absensi::updateOrCreate(
            [
                'anggota_id' => $izin->anggota_id,
                'jadwal_id' => $izin->jadwal_id,
                'tanggal' => $izin->tanggal,
            ],
            [
                'status' => $izin->jenis,
                'sumber' => Absensi::SUMBER_IZIN_DISETUJUI,
                'izin_sakit_id' => $izin->id,
            ]
        );

        return $absensi;
    }
}
