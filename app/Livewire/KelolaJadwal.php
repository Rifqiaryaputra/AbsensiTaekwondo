<?php

namespace App\Livewire;

use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class KelolaJadwal extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $hari = 'Senin';

    public string $jamMulai = '16:00';

    public string $jamTutup = '18:00';

    public array $petugasTerpilih = [];

    public bool $showDelete = false;

    public ?int $deleteId = null;

    public string $deleteHari = '';

    public array $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function jadwalList(): \Illuminate\Database\Eloquent\Collection
    {
        $urutan = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];

        return Jadwal::query()
            ->with('petugas')
            ->get()
            ->sortBy(fn ($j) => $urutan[$j->hari] ?? 7)
            ->values();
    }

    public function daftarPetugas(): \Illuminate\Database\Eloquent\Collection
    {
        return User::query()
            ->with('anggota')
            ->where('role', User::ROLE_PETUGAS)
            ->orderBy('id')
            ->get();
    }

    #[On('tambahJadwal')]
    public function openForm(): void
    {
        $this->showForm = true;
        $this->editingId = null;
        $this->hari = 'Senin';
        $this->jamMulai = '16:00';
        $this->jamTutup = '18:00';
        $this->petugasTerpilih = [];
    }

    public function edit(int $id): void
    {
        $jadwal = Jadwal::with('petugas')->findOrFail($id);

        $this->showForm = true;
        $this->editingId = $jadwal->id;
        $this->hari = $jadwal->hari;
        $this->jamMulai = substr($jadwal->jam_start, 0, 5);
        $this->jamTutup = substr($jadwal->jam_close, 0, 5);
        $this->petugasTerpilih = $jadwal->petugas->pluck('id')->map(fn ($petugasId) => (string) $petugasId)->all();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->petugasTerpilih = [];
    }

    public function save(): void
    {
        $this->validate([
            'hari' => ['required', Rule::in($this->daftarHari)],
            'jamMulai' => ['required'],
            'jamTutup' => ['required'],
            'petugasTerpilih' => ['required', 'array', 'min:1', 'max:2'],
        ]);

        $jadwal = $this->editingId ? Jadwal::findOrFail($this->editingId) : new Jadwal;

        $jadwal->hari = $this->hari;
        $jadwal->jam_start = $this->jamMulai.':00';
        $jadwal->jam_close = $this->jamTutup.':00';
        $jadwal->save();

        $jadwal->petugas()->sync(array_map('intval', $this->petugasTerpilih));

        $this->closeForm();
        $this->dispatch('toast', title: 'Berhasil', message: $this->editingId ? 'Jadwal berhasil diperbarui.' : 'Jadwal baru berhasil ditambahkan.', type: 'success');
    }

    public function openDelete(int $id, string $hari): void
    {
        $this->deleteId = $id;
        $this->deleteHari = $hari;
        $this->showDelete = true;
    }

    public function closeDelete(): void
    {
        $this->showDelete = false;
        $this->deleteId = null;
    }

    public function confirmDelete(): void
    {
        $jadwal = Jadwal::find($this->deleteId);
        
        if ($jadwal) {
            // 1. Sapu bersih data absensi yang menempel pada jadwal ini terlebih dahulu
            \App\Models\Absensi::where('jadwal_id', $this->deleteId)->delete();

            // 2. Sapu bersih pengajuan izin/sakit yang menempel pada jadwal ini
            \App\Models\IzinSakit::where('jadwal_id', $this->deleteId)->delete();

            // 3. Setelah data anak bersih, barulah jadwal bisa dihapus dengan aman
            $jadwal->delete();
        }

        $this->closeDelete();
        $this->dispatch('toast', title: 'Dihapus', message: 'Jadwal beserta data relasinya telah dihapus.', type: 'success');
    }

    public function render()
    {
        return view('livewire.kelola-jadwal', [
            'jadwal' => $this->jadwalList(),
            'petugas' => $this->daftarPetugas(),
        ]);
    }
}