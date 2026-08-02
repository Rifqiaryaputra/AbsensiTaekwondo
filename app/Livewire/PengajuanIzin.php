<?php

namespace App\Livewire;

use App\Models\IzinSakit;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;

class PengajuanIzin extends Component
{
    use WithFileUploads;

    public bool $showForm = false;

    public string $tanggal = '';

    public string $jenis = 'izin';

    public string $keterangan = '';

    public $bukti;

    public ?string $jadwalInfo = null;

    public ?string $batasInfo = null;

    public function updatedTanggal(JadwalService $service): void
    {
        $this->resolveJadwalInfo($service);
    }

    private function resolveJadwalInfo(JadwalService $service): void
    {
        $this->jadwalInfo = null;
        $this->batasInfo = null;

        if ($this->tanggal === '') {
            return;
        }

        $jadwal = $service->getJadwalUntukTanggal($this->tanggal);

        if ($jadwal) {
            $this->jadwalInfo = "Jadwal: {$jadwal->hari} ({$jadwal->jam_start} - {$jadwal->jam_close} WIB)";
            $this->batasInfo = $service->batasPengajuan($jadwal, $this->tanggal)->translatedFormat('l, d M Y H:i');
        } else {
            $this->jadwalInfo = 'Tidak ada jadwal latihan pada tanggal tersebut.';
        }
    }

    public function save(JadwalService $service): void
    {
        $anggota = auth()->user()?->anggota;

        if (! $anggota) {
            $this->dispatch('toast', title: 'Gagal', message: 'Akun Anda tidak terhubung ke data anggota.', type: 'error');

            return;
        }

        $this->validate([
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'in:izin,sakit'],
            'keterangan' => ['required', 'string', 'max:1000'],
            'bukti' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:2048'],
        ]);

        $tanggal = Carbon::parse($this->tanggal);

        if ($tanggal->isPast()) {
            $this->dispatch('toast', title: 'Gagal', message: 'Tanggal pengajuan tidak boleh di masa lalu.', type: 'error');

            return;
        }

        if ($service->isHariLibur($tanggal)) {
            $this->dispatch('toast', title: 'Gagal', message: 'Tanggal tersebut merupakan hari libur latihan.', type: 'error');

            return;
        }

        $jadwal = $service->getJadwalUntukTanggal($tanggal);

        if (! $jadwal) {
            $this->dispatch('toast', title: 'Gagal', message: 'Tidak ada jadwal latihan pada tanggal tersebut.', type: 'error');

            return;
        }

        // IZIN-2: tolak bila diajukan kurang dari 2 jam sebelum jam start.
        $batas = $service->batasPengajuan($jadwal, $tanggal);
        if (now()->greaterThan($batas)) {
            $this->dispatch('toast', title: 'Ditolak', message: 'Pengajuan harus diajukan minimal 2 jam sebelum jam mulai absen (batas: '.$batas->format('d M Y H:i').').', type: 'error');

            return;
        }

        $buktiPath = null;
        if ($this->bukti) {
            $dir = public_path('bukti-izin');
            File::ensureDirectoryExists($dir);
            $name = $anggota->id.'_'.now()->timestamp.'.'.($this->bukti->getClientOriginalExtension() ?: 'jpg');
            File::copy($this->bukti->getRealPath(), $dir.DIRECTORY_SEPARATOR.$name);
            $buktiPath = 'bukti-izin/'.$name;
        }

        IzinSakit::create([
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => $this->tanggal,
            'jenis' => $this->jenis,
            'keterangan' => $this->keterangan,
            'bukti_lampiran' => $buktiPath,
            'status' => IzinSakit::STATUS_MENUNGGU,
            'diajukan_pada' => now(),
        ]);

        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('toast', title: 'Berhasil', message: 'Pengajuan '.ucfirst($this->jenis).' berhasil dikirim.', type: 'success');
    }

    public function batalkan(int $id): void
    {
        $izin = $this->pengajuanQuery()->find($id);

        if ($izin && $izin->status === IzinSakit::STATUS_MENUNGGU) {
            $izin->update(['status' => IzinSakit::STATUS_DIBATALKAN]);
            $this->dispatch('toast', title: 'Dibatalkan', message: 'Pengajuan telah dibatalkan.', type: 'info');
        }
    }

    public function toggleForm(bool $show): void
    {
        $this->showForm = $show;
        if ($show) {
            $this->resetForm();
            $this->resolveJadwalInfo(app(JadwalService::class));
        }
    }

    public function pengajuanQuery()
    {
        return IzinSakit::query()
            ->where('anggota_id', auth()->user()?->anggota?->id)
            ->orderByDesc('diajukan_pada');
    }

    public function pengajuanList()
    {
        return $this->pengajuanQuery()->get();
    }

    private function resetForm(): void
    {
        $this->tanggal = '';
        $this->jenis = 'izin';
        $this->keterangan = '';
        $this->bukti = null;
        $this->jadwalInfo = null;
        $this->batasInfo = null;
    }

    public function render()
    {
        return view('livewire.pengajuan-izin', [
            'pengajuan' => $this->pengajuanList(),
        ]);
    }
}
