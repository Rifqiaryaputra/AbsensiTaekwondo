<?php

namespace App\Http\Controllers;

use App\Jobs\TutupSesiAbsen;
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
     * Beban berat (rekap Alfa) dipindahkan ke queued job agar respons cepat
     * dan menghindari gateway timeout (504) di shared hosting.
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

        TutupSesiAbsen::dispatch($jadwal->id);

        return back()->with('success', 'Sesi absensi ditutup. Anggota yang belum hadir sedang direkap sebagai Alfa.');
    }
}
