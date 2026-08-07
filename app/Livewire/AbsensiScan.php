<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\User;
use App\Services\AbsensiService;
use App\Services\JadwalService;
use Carbon\Carbon;
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

    public ?string $sesiTanggal = null;

    public ?string $sesiMode = null;

    public bool $isLibur = false;

    public string $namaLibur = '';

    public ?string $tanggalLibur = null;

    public Collection $records;

    public function mount(JadwalService $jadwalService): void
    {
        $this->refreshJadwal($jadwalService);

        if ($message = session()->pull('success')) {
            $this->dispatch('toast', title: 'Berhasil', message: $message, type: 'success');
        } elseif ($message = session()->pull('error')) {
            $this->dispatch('toast', title: 'Gagal', message: $message, type: 'error');
        }
    }

    public function refreshJadwal(JadwalService $jadwalService): void
    {
        $now = now();
        $holiday = HariLibur::query()->whereDate('tanggal', $now->toDateString())->first();

        $this->isLibur = $holiday !== null;
        $this->namaLibur = $holiday?->keterangan ?? '';
        $this->tanggalLibur = $holiday?->tanggal?->toDateString() ?? null;

        if ($holiday) {
            $this->jadwalId = null;
            $this->jadwalInfo = null;
            $this->batasTutup = null;
            $this->jadwalHariIniInfo = null;
            $this->jadwalHariIniStatus = null;
            $this->sesiTanggal = null;
            $this->sesiMode = null;
            $this->records = new Collection;

            return;
        }

        $jadwal = $jadwalService->getActiveJadwal($now);

        $this->jadwalId = $jadwal?->id;
        $this->jadwalInfo = null;
        $this->batasTutup = null;
        $this->jadwalHariIniInfo = null;
        $this->jadwalHariIniStatus = null;
        $this->sesiTanggal = null;
        $this->sesiMode = null;

        if ($jadwal) {
            $this->jadwalInfo = "Latihan Rutin {$jadwal->hari} | {$jadwal->jam_start} - {$jadwal->jam_close} WIB";
            $this->batasTutup = "{$jadwal->jam_close} WIB";
            $this->sesiTanggal = $now->toDateString();
            $this->sesiMode = 'live';

            $this->loadRecords();

            return;
        }

        $kemarin = $now->copy()->subDay();
        $sesiKemarinAda = Absensi::query()->whereDate('tanggal', $kemarin->toDateString())->exists()
            || $jadwalService->getJadwalUntukTanggal($kemarin) !== null;

        if ($sesiKemarinAda) {
            $windowEnd = $kemarin->copy()->addDay()->setHour(13)->setMinute(0);

            if ($now->lessThan($windowEnd)) {
                $this->sesiTanggal = $kemarin->toDateString();
                $this->sesiMode = 'koreksi';

                $this->loadRecords();

                return;
            }

            if (! $jadwalService->getJadwalUntukTanggal($now)) {
                $this->sesiMode = 'terkunci';
                $this->loadRecords();

                return;
            }
        }

        $jadwalHariIni = $jadwalService->getJadwalUntukTanggal($now);
        if ($jadwalHariIni) {
            $this->jadwalHariIniInfo = "{$jadwalHariIni->hari} ({$jadwalHariIni->jam_start} - {$jadwalHariIni->jam_close} WIB)";
            $this->jadwalHariIniStatus = $now->toTimeString() < $jadwalHariIni->jam_start ? 'belum dibuka' : 'sudah ditutup';
            $this->sesiTanggal = $now->toDateString();
        } else {
            $this->jadwalHariIniInfo = null;
            $this->jadwalHariIniStatus = null;
        }

        $this->loadRecords();
    }

    public function loadRecords(): void
    {
        $this->records = Absensi::query()
            ->with('anggota')
            ->when($this->jadwalId, fn ($q) => $q->where('jadwal_id', $this->jadwalId))
            ->whereDate('tanggal', $this->sesiTanggal ?? now()->toDateString())
            ->orderByDesc('waktu_scan')
            ->orderByDesc('id')
            ->get();
    }

    public function canEditAbsensi(): bool
    {
        $user = auth()->user();
        $isAuthorizedRole = $user && in_array($user->role, [User::ROLE_ADMIN, User::ROLE_PETUGAS], true);

        // Jika bukan Admin/Petugas, langsung blokir (hilangkan tombol)
        if (! $isAuthorizedRole || ! $this->sesiTanggal) {
            return false;
        }

        // Admin bebas dari batasan waktu: bisa edit kapan pun.
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // KONDISI 1: Boleh edit jika jadwal absensi sedang berjalan (live) hari ini
        if ($this->sesiMode === 'live') {
            return true;
        }

        // KONDISI 2: Boleh edit jika sedang berada di jendela koreksi (besok harinya jam 12:00 - 13:00)
        $now = now();
        $jadwalDate = Carbon::parse($this->sesiTanggal);
        $nextDay = $jadwalDate->copy()->addDay()->startOfDay();

        $windowStart = $nextDay->copy()->setHour(12)->setMinute(0);
        $windowEnd = $nextDay->copy()->setHour(13)->setMinute(0);

        return $now->between($windowStart, $windowEnd);
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

        // --- VALIDASI STATUS ANGGOTA (INPUT MANUAL) ---
        if ($anggota->status_anggota !== 'aktif') {
            $statusTeks = ucfirst($anggota->status_anggota);
            $this->dispatch('toast', title: 'Akses Ditolak', message: "Anggota ini berstatus {$statusTeks} dan tidak dapat diabsen.", type: 'warning');
            $this->nim = ''; // Bersihkan input
            return;
        }
        // ----------------------------------------------

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

        // --- VALIDASI STATUS ANGGOTA (SCAN QR) ---
        if ($anggota->status_anggota !== 'aktif') {
            $statusTeks = ucfirst($anggota->status_anggota);
            $this->dispatch('toast', title: 'Akses Ditolak', message: "Anggota dengan nama {$anggota->nama_lengkap} berstatus {$statusTeks}.", type: 'warning');
            return;
        }
        // -----------------------------------------

        $result = $service->catatKehadiran($anggota, Absensi::SUMBER_SCAN, auth()->id());
        $this->dispatchToast($result);
        $this->loadRecords();
    }

    #[On('changeStatus')]
    public function updateStatus(int|string $absensiId, string $status, AbsensiService $service): void
    {
        if (! $this->canEditAbsensi()) {
            abort(403, 'Akses ditolak. Sesi absen sedang tidak aktif atau di luar jam perbaikan (12.00 - 13.00 WIB keesokan harinya).');
        }

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