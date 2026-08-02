<?php

namespace App\Livewire;

use App\Models\IzinSakit;
use Carbon\Carbon;
use Livewire\Component;

class KelolaPerizinan extends Component
{
    public string $search = '';

    public string $statusFilter = '';

    public string $dateFilter = '';

    public function setStatus(int $id, string $status): void
    {
        if (! in_array($status, [IzinSakit::STATUS_DISETUJUI, IzinSakit::STATUS_DITOLAK])) {
            return;
        }

        $izin = IzinSakit::find($id);

        if (! $izin) {
            return;
        }

        $izin->update([
            'status' => $status,
            'diproses_oleh' => auth()->id(),
            'diproses_pada' => now(),
        ]);

        $this->dispatch('toast',
            title: $status === IzinSakit::STATUS_DISETUJUI ? 'Disetujui' : 'Ditolak',
            message: $status === IzinSakit::STATUS_DISETUJUI ? 'Pengajuan izin berhasil disetujui.' : 'Pengajuan izin telah ditolak.',
            type: $status === IzinSakit::STATUS_DISETUJUI ? 'success' : 'error'
        );
    }

    public function filtered(): \Illuminate\Database\Eloquent\Collection
    {
        $search = strtolower(trim($this->search));

        return IzinSakit::query()
            ->with(['anggota', 'jadwal'])
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('anggota', function ($q) use ($search) {
                    $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(nim) LIKE ?', ["%{$search}%"]);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->dateFilter !== '', fn ($query) => $query->whereDate('tanggal', $this->dateFilter))
            ->orderByDesc('diajukan_pada')
            ->get();
    }

    public function formatTanggal(string $date): string
    {
        return Carbon::parse($date)->translatedFormat('l, d M Y');
    }

    public function render()
    {
        return view('livewire.kelola-perizinan', [
            'pengajuan' => $this->filtered(),
        ]);
    }
}
