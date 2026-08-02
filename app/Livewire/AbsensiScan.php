<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Services\AbsensiService;
use App\Services\JadwalService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class AbsensiScan extends Component
{
    public string $nim = '';

    public ?string $jadwalInfo = null;

    public ?string $batasTutup = null;

    public ?int $jadwalId = null;

    public ?string $jadwalHariIniInfo = null;

    public ?string $jadwalHariIniStatus = null;

    public Collection $records;

    public function mount(JadwalService $jadwalService): void
    {
        $this->refreshJadwal($jadwalService);
    }

    public function refreshJadwal(JadwalService $jadwalService): void
    {
        $now = now();
        $jadwal = $jadwalService->getActiveJadwal($now);

        $this->jadwalId = $jadwal?->id;

        if ($jadwal) {
            $this->jadwalInfo = "Latihan Rutin {$jadwal->hari} | {$jadwal->jam_start} - {$jadwal->jam_close} WIB";
            $this->batasTutup = "{$jadwal->jam_close} WIB";
            $this->jadwalHariIniInfo = null;
            $this->jadwalHariIniStatus = null;
        } else {
            $this->jadwalInfo = null;
            $this->batasTutup = null;

            $jadwalHariIni = $jadwalService->getJadwalUntukTanggal($now);
            if ($jadwalHariIni) {
                $this->jadwalHariIniInfo = "{$jadwalHariIni->hari} ({$jadwalHariIni->jam_start} - {$jadwalHariIni->jam_close} WIB)";
                $this->jadwalHariIniStatus = $now->toTimeString() < $jadwalHariIni->jam_start ? 'belum dibuka' : 'sudah ditutup';
            } else {
                $this->jadwalHariIniInfo = null;
                $this->jadwalHariIniStatus = null;
            }
        }

        $this->loadRecords();
    }

    public function loadRecords(): void
    {
        $this->records = Absensi::query()
            ->with('anggota')
            ->when($this->jadwalId, fn ($q) => $q->where('jadwal_id', $this->jadwalId))
            ->whereDate('tanggal', now()->toDateString())
            ->orderByDesc('waktu_scan')
            ->orderByDesc('id')
            ->get();
    }

    public function processManualInput(AbsensiService $service): void
    {
        $nim = trim($this->nim);

        if ($nim === '') {
            $this->dispatch('toast', title: 'Masukkan NIM', message: 'NIM tidak boleh kosong', type: 'warning');

            return;
        }

        $anggota = Anggota::where('nim', $nim)->first();

        if (! $anggota) {
            $this->dispatch('toast', title: 'Gagal', message: "NIM {$nim} tidak ditemukan", type: 'error');

            return;
        }

        $result = $service->catatKehadiran($anggota, Absensi::SUMBER_MANUAL, auth()->id());
        $this->dispatchToast($result);
        $this->nim = '';
        $this->loadRecords();
    }

    #[On('scanResult')]
    public function processScanInput(string $idAnggota, AbsensiService $service): void
    {
        $idAnggota = trim($idAnggota);

        if ($idAnggota === '') {
            return;
        }

        $anggota = Anggota::where('id_anggota', $idAnggota)->first();

        if (! $anggota) {
            $this->dispatch('toast', title: 'Gagal', message: "ID Anggota {$idAnggota} tidak ditemukan", type: 'error');

            return;
        }

        $result = $service->catatKehadiran($anggota, Absensi::SUMBER_SCAN, auth()->id());
        $this->dispatchToast($result);
        $this->loadRecords();
    }

    #[On('changeStatus')]
    public function updateStatus(int $absensiId, string $status, AbsensiService $service): void
    {
        $absensi = Absensi::find($absensiId);

        if (! $absensi) {
            return;
        }

        $service->perbaruiStatus($absensi, $status, auth()->id());
        $this->dispatch('toast', title: 'Status Diperbarui', message: 'Status kehadiran berhasil diubah.', type: 'success');
        $this->loadRecords();
    }

    public function countStatus(string $status): int
    {
        return $this->records->where('status', $status)->count();
    }

    private function dispatchToast(array $result): void
    {
        $this->dispatch('toast', title: $result['ok'] ? 'Berhasil' : 'Gagal', message: $result['message'], type: $result['ok'] ? 'success' : 'error');
    }

    public function render()
    {
        return view('livewire.absensi-scan');
    }
}
