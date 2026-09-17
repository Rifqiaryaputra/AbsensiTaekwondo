<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\HariLibur;
use Illuminate\Support\Carbon;
use Illuminate\Support\CarbonPeriod;
use Livewire\Attributes\On;
use Livewire\Component;

class KelolaHariLibur extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $tanggal_mulai = '';

    public string $tanggal_akhir = '';

    public string $keterangan = '';

    public bool $showDelete = false;

    public ?int $deleteId = null;

    public string $deleteTanggal = '';

    public function liburList(): \Illuminate\Database\Eloquent\Collection
    {
        return HariLibur::query()
            ->orderBy('tanggal')
            ->get();
    }

    #[On('tambahLibur')]
    public function openForm(int $id = 0): void
    {
        $this->showForm = true;
        $this->editingId = $id > 0 ? $id : null;
        $this->tanggal_mulai = '';
        $this->tanggal_akhir = '';
        $this->keterangan = '';

        if ($this->editingId) {
            $libur = HariLibur::find($this->editingId);
            if ($libur) {
                $this->tanggal_mulai = $libur->tanggal->format('Y-m-d');
                $this->tanggal_akhir = $libur->tanggal->format('Y-m-d');
                $this->keterangan = $libur->keterangan;
            }
        }
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->tanggal_mulai = '';
        $this->tanggal_akhir = '';
        $this->keterangan = '';
    }

    public function save(): void
    {
        $this->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_akhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['required', 'string', 'max:255'],
        ]);

        if ($this->editingId) {
            $this->saveSingle();
        } else {
            $this->tanggal_akhir = $this->tanggal_akhir ?: $this->tanggal_mulai;
            $this->saveRange();
        }

        $this->closeForm();
        $this->dispatch('toast', title: 'Berhasil', message: $this->editingId ? 'Data libur berhasil diperbarui.' : 'Data libur berhasil ditambahkan.', type: 'success');
    }

    private function saveSingle(): void
    {
        $libur = HariLibur::findOrFail($this->editingId);

        $libur->tanggal = $this->tanggal_mulai;
        $libur->keterangan = $this->keterangan;
        $libur->save();

        $this->rollbackAlfa($this->tanggal_mulai);
    }

    private function saveRange(): void
    {
        $period = CarbonPeriod::create($this->tanggal_mulai, $this->tanggal_akhir);

        foreach ($period as $date) {
            $tanggal = $date->format('Y-m-d');

            if (! HariLibur::query()->whereDate('tanggal', $tanggal)->exists()) {
                HariLibur::query()->create([
                    'tanggal' => $tanggal,
                    'keterangan' => $this->keterangan,
                ]);
            }

            $this->rollbackAlfa($tanggal);
        }
    }

    private function rollbackAlfa(string $tanggalLibur): void
    {
        $tanggal = Carbon::parse($tanggalLibur);

        if ($tanggal->greaterThan(now())) {
            return;
        }

        Absensi::query()
            ->whereDate('tanggal', $tanggal->toDateString())
            ->where('status', Absensi::STATUS_ALFA)
            ->delete();
    }

    public function openDelete(int $id, string $tanggal): void
    {
        $this->deleteId = $id;
        $this->deleteTanggal = $tanggal;
        $this->showDelete = true;
    }

    public function closeDelete(): void
    {
        $this->showDelete = false;
        $this->deleteId = null;
    }

    public function confirmDelete(): void
    {
        $libur = HariLibur::find($this->deleteId);
        if ($libur) {
            $libur->delete();
        }

        $this->closeDelete();
        $this->dispatch('toast', title: 'Dihapus', message: 'Data libur berhasil dihapus.', type: 'success');
    }

    public function render()
    {
        return view('livewire.kelola-hari-libur', [
            'libur' => $this->liburList(),
        ]);
    }
}
