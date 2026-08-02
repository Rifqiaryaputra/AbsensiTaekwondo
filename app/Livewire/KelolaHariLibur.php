<?php

namespace App\Livewire;

use App\Models\HariLibur;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class KelolaHariLibur extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $tanggal = '';

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
        $this->tanggal = '';
        $this->keterangan = '';

        if ($this->editingId) {
            $libur = HariLibur::find($this->editingId);
            if ($libur) {
                $this->tanggal = $libur->tanggal->format('Y-m-d');
                $this->keterangan = $libur->keterangan;
            }
        }
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->tanggal = '';
        $this->keterangan = '';
    }

    public function save(): void
    {
        $this->validate([
            'tanggal' => ['required', 'date', Rule::unique('hari_libur', 'tanggal')->ignore($this->editingId)],
            'keterangan' => ['required', 'string', 'max:255'],
        ]);

        $libur = $this->editingId ? HariLibur::findOrFail($this->editingId) : new HariLibur;

        $libur->tanggal = $this->tanggal;
        $libur->keterangan = $this->keterangan;
        $libur->save();

        $this->closeForm();
        $this->dispatch('toast', title: 'Berhasil', message: $this->editingId ? 'Data libur berhasil diperbarui.' : 'Data libur berhasil ditambahkan.', type: 'success');
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
