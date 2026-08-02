<?php

namespace App\Observers;

use App\Models\IzinSakit;
use App\Services\AbsensiService;

class IzinSakitObserver
{
    /**
     * Saat status pengajuan berubah menjadi "disetujui", otomatis catat/perbarui
     * absensi anggota pada tanggal & jadwal terkait (ABSEN-7 / IZIN-6).
     */
    public function updated(IzinSakit $izin): void
    {
        if (! $izin->isDirty('status')) {
            return;
        }

        if ($izin->status === IzinSakit::STATUS_DISETUJUI) {
            app(AbsensiService::class)->recordIzinDisetujui($izin);
        }
    }
}
