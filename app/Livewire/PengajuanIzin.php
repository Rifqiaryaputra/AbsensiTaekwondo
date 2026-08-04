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
        $this->resetValidation('tanggal');
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

        if ($tanggal->isBefore(Carbon::today())) {
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
            $this->addError('tanggal', 'Batas waktu pengajuan maksimal 2 jam sebelum latihan.');
            $this->dispatch('toast', title: 'Ditolak', message: 'Batas waktu pengajuan telah habis. Maksimal 2 jam sebelum latihan dimulai.', type: 'error');

            return;
        }

        // QUOTA-1: maksimal 5 pengajuan izin/sakit per bulan kalender (selain yang dibatalkan).
        $kuotaBulan = IzinSakit::query()
            ->where('anggota_id', $anggota->id)
            ->whereIn('jenis', [IzinSakit::JENIS_IZIN, IzinSakit::JENIS_SAKIT])
            ->whereMonth('tanggal', $tanggal->month)
            ->whereYear('tanggal', $tanggal->year)
            ->where('status', '!=', IzinSakit::STATUS_DIBATALKAN)
            ->count();

        if ($kuotaBulan >= 5) {
            $this->addError('tanggal', 'Kuota izin/sakit bulan ini telah habis (Maks. 5 kali).');
            $this->dispatch('toast', title: 'Ditolak', message: 'Kuota izin/sakit bulan ini telah habis (Maks. 5 kali per bulan).', type: 'error');

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

        $jenis = $this->jenis;

        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('toast', title: 'Berhasil', message: 'Pengajuan '.ucfirst($jenis).' berhasil dikirim.', type: 'success');
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
            $this->resetValidation();
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
        // Tampilkan maks. 3 pengajuan terbaru, dan sembunyikan sesi yang sudah
        // berumur lebih dari 1 hari (H+1) dari tanggal jadwal.
        return $this->pengajuanQuery()
            ->where('tanggal', '>=', now()->subDay()->toDateString())
            ->limit(3)
            ->get();
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
