<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class KelolaPetugas extends Component
{
    public string $searchAnggota = '';

    public ?int $selectedUserId = null;

    public string $selectedNama = '';

    public string $selectedNim = '';

    public ?int $editingId = null;

    public bool $showForm = false;

    public bool $showDelete = false;

    public ?int $deleteId = null;

    public string $deleteNama = '';

    public string $email = '';

    public string $password = '';

    public array $avatarColors = ['#4F46E5', '#6860ef', '#0ea5e9', '#10b981', '#f59e0b', '#ec4899'];

    public function petugasList(): \Illuminate\Database\Eloquent\Collection
    {
        return User::query()
            ->with(['anggota', 'jadwalPetugas'])
            ->where('role', User::ROLE_PETUGAS)
            ->orderBy('id')
            ->get();
    }

    public function updatedSearchAnggota(): void
    {
        $this->selectedUserId = null;
        $this->selectedNama = '';
        $this->selectedNim = '';
    }

    public function searchCandidates(): \Illuminate\Support\Collection
    {
        $search = strtolower(trim($this->searchAnggota));

        if ($search === '') {
            return collect();
        }

        return User::query()
            ->with('anggota')
            ->where('role', User::ROLE_ANGGOTA)
            ->whereHas('anggota', function ($q) use ($search) {
                $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(nim) LIKE ?', ["%{$search}%"]);
            })
            ->limit(10)
            ->get();
    }

    public function selectAnggota(int $userId, string $nama, string $nim): void
    {
        $this->selectedUserId = $userId;
        $this->selectedNama = $nama;
        $this->selectedNim = $nim;
        $this->searchAnggota = '';
    }

    public function resetSelection(): void
    {
        $this->selectedUserId = null;
        $this->selectedNama = '';
        $this->selectedNim = '';
        $this->searchAnggota = '';
    }

    #[On('tambahPetugas')]
    public function openForm(int $id = 0): void
    {
        $this->showForm = true;
        $this->editingId = $id > 0 ? $id : null;
        $this->resetSelection();
        $this->email = '';
        $this->password = '';

        if ($this->editingId) {
            $user = User::find($this->editingId);
            if ($user) {
                $this->email = $user->email;
                if ($user->anggota) {
                    $this->selectedUserId = $user->id;
                    $this->selectedNama = $user->anggota->nama_lengkap;
                    $this->selectedNim = $user->anggota->nim;
                }
            }
        }
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetSelection();
        $this->email = '';
        $this->password = '';
    }

    public function save(): void
    {
        $rules = [
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingId)],
        ];

        if ($this->editingId) {
            $rules['password'] = ['nullable', 'string', 'min:6'];
        } else {
            $rules['password'] = ['required', 'string', 'min:6'];
            $rules['selectedUserId'] = ['required'];
        }

        $this->validate($rules);

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->email = $this->email;
            if ($this->password !== '') {
                $user->password = $this->password;
            }
            $user->save();
            $message = 'Data petugas berhasil diperbarui.';
        } else {
            // Role-separation: buat akun PETUGAS BARU tanpa mengubah akun anggota asli.
            $anggotaUser = User::findOrFail($this->selectedUserId);
            $anggotaId = $anggotaUser->anggota_id;

            if (! $anggotaId) {
                $this->dispatch('toast', title: 'Gagal', message: 'Akun anggota tidak terhubung ke data anggota.', type: 'error');

                return;
            }

            $sudahPetugas = User::query()
                ->where('role', User::ROLE_PETUGAS)
                ->where('anggota_id', $anggotaId)
                ->exists();

            if ($sudahPetugas) {
                $this->dispatch('toast', title: 'Gagal', message: 'Anggota ini sudah memiliki akun petugas.', type: 'error');

                return;
            }

            User::create([
                'name' => $anggotaUser->anggota?->nama_lengkap ?? $anggotaUser->name,
                'email' => $this->email,
                'password' => $this->password,
                'role' => User::ROLE_PETUGAS,
                'anggota_id' => $anggotaId,
                'force_password_change' => false,
            ]);
            $message = 'Akun petugas baru berhasil dibuat untuk anggota tersebut.';
        }

        $this->closeForm();
        $this->dispatch('toast', title: 'Berhasil', message: $message, type: 'success');
    }

    public function openDelete(int $id, string $nama): void
    {
        $this->deleteId = $id;
        $this->deleteNama = $nama;
        $this->showDelete = true;
    }

    public function closeDelete(): void
    {
        $this->showDelete = false;
        $this->deleteId = null;
    }

    public function confirmDelete(): void
    {
        $petugas = User::find($this->deleteId);
        if ($petugas && $petugas->role === User::ROLE_PETUGAS) {
            $petugas->delete();
        }

        $this->closeDelete();
        $this->dispatch('toast', title: 'Dihapus', message: 'Akun petugas dihapus. Anggota & akun anggotanya tetap utuh.', type: 'success');
    }

    public function avatarColor(int $id): string
    {
        return $this->avatarColors[$id % count($this->avatarColors)];
    }

    public function render()
    {
        return view('livewire.kelola-petugas', [
            'petugas' => $this->petugasList(),
            'candidates' => $this->searchCandidates(),
        ]);
    }
}
