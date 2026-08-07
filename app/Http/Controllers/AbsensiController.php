<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\IzinSakit;
use App\Models\Jadwal;
use App\Services\JadwalService;
use Illuminate\Http\RedirectResponse;

class AbsensiController extends Controller
{
    public function index()
    {
        // TODO Fase 4: logika jadwal aktif & daftar absensi berasal dari database.
        return view('petugas.absensi');
    }

    /**
     * Tutup sesi absensi secara manual (Tutup Absen).
     * Logika sama dengan cron auto-alfa: anggota tanpa catatan kehadiran
     * (dan tanpa izin/sakit disetujui) pada sesi ini direkap sebagai Alfa.
     */
    public function closeManual(int $id, JadwalService $jadwalService): RedirectResponse
    {
        $jadwal = Jadwal::findOrFail($id);

        if ($jadwal->is_closed) {
            return back()->with('error', 'Sesi absensi jadwal ini sudah ditutup.');
        }

        if ($jadwalService->isHariLibur(now())) {
            return back()->with('error', 'Hari ini merupakan hari libur, sesi absensi tidak dapat ditutup.');
        }

        $tanggal = now()->toDateString();

        $anggotaTanpaKehadiran = Anggota::query()
            ->where('status_anggota', Anggota::STATUS_AKTIF)
            ->whereDoesntHave('absensi', function ($query) use ($jadwal, $tanggal) {
                $query->where('jadwal_id', $jadwal->id)
                    ->whereDate('tanggal', $tanggal);
            })
            ->whereDoesntHave('izinSakit', function ($query) use ($tanggal) {
                $query->whereDate('tanggal', $tanggal)
                    ->where('status', IzinSakit::STATUS_DISETUJUI);
            })
            ->pluck('id');

        foreach ($anggotaTanpaKehadiran->chunk(500) as $chunk) {
            $records = $chunk->map(fn ($anggotaId) => [
                'anggota_id' => $anggotaId,
                'jadwal_id' => $jadwal->id,
                'tanggal' => $tanggal,
                'status' => Absensi::STATUS_ALFA,
                'sumber' => Absensi::SUMBER_OTOMATIS,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            Absensi::insert($records);
        }

        $jadwal->update(['is_closed' => true]);

        return back()->with('success', 'Sesi absensi ditutup. Anggota yang belum hadir direkap sebagai Alfa.');
    }
}
