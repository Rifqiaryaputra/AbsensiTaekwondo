<?php

namespace App\Livewire;

use App\Exports\DatabaseAnggotaExport;
use App\Imports\AnggotaImport;
use App\Models\Anggota;
use App\Services\AnggotaService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class DaftarAnggota extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $fakultasFilter = '';

    public string $prodiFilter = '';

    public int $perPage = 5;

    public bool $showForm = false;

    public bool $showView = false;

    public bool $showDelete = false;

    public bool $showImport = false;

    public $importFile;

    public ?int $editingId = null;

    public ?int $viewingId = null;

    public ?int $deletingId = null;

    public string $nama = '';

    public string $nim = '';

    public string $tglLahir = '';

    public string $jk = '';

    public string $wa = '';

    public string $fakultas = '';

    public string $prodi = '';

    public string $bpjs = '';

    public string $status_anggota = Anggota::STATUS_AKTIF;

    public ?string $fotoLama = null;

    public $foto;

    public array $fakultasProdi = [
        'Agama Islam' => ['Bahasa dan Sastra Arab', 'Ilmu Hadis', 'Pendidikan Agama Islam', 'Perbankan Syariah'],
        'Bisnis dan Ekonomika' => ['Akuntansi', 'Bisnis Jasa Makanan', 'Ekonomi Pembangunan', 'Manajemen'],
        'Hukum' => ['Hukum'],
        'Keguruan dan Ilmu Pendidikan (FKIP)' => ['Bimbingan dan Konseling', 'Pendidikan Bahasa Inggris', 'Pendidikan Bahasa dan Sastra Indonesia', 'Pendidikan Biologi', 'Pendidikan Fisika', 'Pendidikan Guru Pendidikan Anak Usia Dini (PGPAUD)', 'Pendidikan Guru Sekolah Dasar (PGSD)', 'Pendidikan Matematika', 'Pendidikan Pancasila dan Kewarganegaraan', 'Pendidikan Profesi Guru'],
        'Kesehatan Masyarakat' => ['Gizi', 'Kesehatan Masyarakat'],
        'Matematika dan Ilmu Pengetahuan Alam (FMIPA)' => ['Biologi', 'Fisika', 'Matematika', 'Sistem Informasi'],
        'Psikologi' => ['Psikologi'],
        'Sastra, Budaya, dan Komunikasi' => ['Ilmu Komunikasi', 'Sastra Indonesia', 'Sastra Inggris'],
        'Teknologi Industri' => ['Informatika', 'Teknik Elektro', 'Teknik Industri', 'Teknik Kimia', 'Teknologi Pangan'],
        'Kedokteran' => ['Farmasi', 'Kedokteran Umum'],
    ];

    public function fakultasList(): array
    {
        return array_keys($this->fakultasProdi);
    }

    public function prodiList(): array
    {
        return $this->fakultasProdi[$this->fakultas] ?? [];
    }

    public function prodiFilterList(): array
    {
        if ($this->fakultasFilter === '') {
            $prodis = Arr::flatten($this->fakultasProdi);
            $prodis = array_values(array_unique($prodis));
            sort($prodis, SORT_STRING);

            return $prodis;
        }

        return $this->fakultasProdi[$this->fakultasFilter] ?? [];
    }

    public function updatedFakultas(): void
    {
        if ($this->prodi !== '' && ! in_array($this->prodi, $this->prodiList(), true)) {
            $this->prodi = '';
        }
    }

    public function updatedFakultasFilter(): void
    {
        // Prodi filter bersifat independen: tidak di-reset saat fakultas berubah.
        $this->resetPage();
    }

    protected function filteredQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $search = strtolower(trim($this->search));

        return Anggota::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(nama_lengkap) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(nim) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(id_anggota) LIKE ?', ["%{$search}%"]);
                });
            })
            ->when($this->fakultasFilter !== '', fn ($query) => $query->where('fakultas', $this->fakultasFilter))
            ->when($this->prodiFilter !== '', fn ($query) => $query->where('program_studi', $this->prodiFilter));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedProdiFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'fakultasFilter', 'prodiFilter']);
        $this->resetPage();
    }

    #[On('tambahAnggota')]
    public function openForm(int $id = 0): void
    {
        $this->resetForm();
        $this->foto = null;
        $this->fotoLama = null;
        $this->editingId = $id > 0 ? $id : null;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $item = Anggota::findOrFail($id);

        $this->resetForm();
        $this->foto = null;
        $this->fotoLama = $item->foto_dobok;
        $this->editingId = $item->id;
        $this->nama = $item->nama_lengkap;
        $this->nim = $item->nim;
        $this->tglLahir = $item->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->jk = $item->jenis_kelamin;
        $this->wa = $item->no_whatsapp ?? '';
        $this->fakultas = $item->fakultas;
        $this->prodi = $item->program_studi;
        $this->bpjs = $item->no_bpjs ?? '';
        $this->status_anggota = $item->status_anggota ?? Anggota::STATUS_AKTIF;
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetForm();
        $this->foto = null;
        $this->fotoLama = null;
    }

    public function save(): void
    {
        $this->nama = ucwords(trim($this->nama));
        $this->wa = trim($this->wa);

        $this->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'nim' => ['required', 'numeric', 'digits:10', Rule::unique('anggota', 'nim')->ignore($this->editingId)],
            'tglLahir' => ['required', 'date'],
            'jk' => ['required', Rule::in(['L', 'P'])],
            'wa' => ['required', 'numeric', 'starts_with:62', 'digits_between:10,15'],
            'fakultas' => ['required', Rule::in($this->fakultasList())],
            'prodi' => ['required', Rule::in($this->prodiList())],
            'bpjs' => ['nullable', 'string', 'max:50'],
            'status_anggota' => ['required', Rule::in(Anggota::statusList())],
            'foto' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $service = app(AnggotaService::class);

        try {
            DB::transaction(function () use ($service) {
                if ($this->editingId) {
                    $this->updateAnggota($service);
                } else {
                    $this->createAnggota($service);
                }
            });
        } catch (\Throwable $e) {
            $this->notify($e->getMessage(), 'Gagal', 'error');

            return;
        }

        $this->closeForm();
        $this->resetPage();
        $this->notify('Data anggota berhasil disimpan.');
    }

    private function createAnggota(AnggotaService $service): void
    {
        $idAnggota = $service->generateIdAnggota($this->nim);
        $qrCode = $service->generateQrCode($idAnggota);

        $anggota = Anggota::create([
            'id_anggota' => $idAnggota,
            'nama_lengkap' => $this->nama,
            'nim' => $this->nim,
            'tanggal_lahir' => $this->tglLahir,
            'jenis_kelamin' => $this->jk,
            'no_whatsapp' => $this->wa,
            'fakultas' => $this->fakultas,
            'program_studi' => $this->prodi,
            'no_bpjs' => $this->bpjs !== '' ? $this->bpjs : null,
            'status_anggota' => $this->status_anggota,
            'qr_code' => $qrCode,
            'foto_dobok' => $this->foto ? $service->storeFotoDobok($this->foto, $idAnggota) : null,
        ]);

        $service->createUser($anggota);
    }

    private function updateAnggota(AnggotaService $service): void
    {
        $anggota = Anggota::findOrFail($this->editingId);
        $nimBerubah = $anggota->nim !== $this->nim;

        if ($nimBerubah) {
            // Regenerasi identitas + QR + email login bila NIM diubah.
            $service->deleteFile($anggota->qr_code);
            $idAnggota = $service->generateIdAnggota($this->nim);
            $anggota->id_anggota = $idAnggota;
            $anggota->qr_code = $service->generateQrCode($idAnggota);

            if ($anggota->user) {
                $anggota->user->update([
                    'name' => $this->nama,
                    'email' => $this->nim.'@webmail.uad.ac.id',
                ]);
            }
        }

        $anggota->nama_lengkap = $this->nama;
        $anggota->tanggal_lahir = $this->tglLahir;
        $anggota->jenis_kelamin = $this->jk;
        $anggota->no_whatsapp = $this->wa;
        $anggota->fakultas = $this->fakultas;
        $anggota->program_studi = $this->prodi;
        $anggota->no_bpjs = $this->bpjs !== '' ? $this->bpjs : null;
        $anggota->status_anggota = $this->status_anggota;

        if ($this->foto) {
            if ($anggota->foto_dobok) {
                $service->deleteFile($anggota->foto_dobok);
            }
            $anggota->foto_dobok = $service->storeFotoDobok($this->foto, $anggota->id_anggota);
        }

        $anggota->save();
    }

    public function openView(int $id): void
    {
        $this->viewingId = $id;
        $this->showView = true;
    }

    public function closeView(): void
    {
        $this->showView = false;
        $this->viewingId = null;
    }

    public function openDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDelete = true;
    }

    public function closeDelete(): void
    {
        $this->showDelete = false;
        $this->deletingId = null;
    }

    public function confirmDelete(): void
    {
        $service = app(AnggotaService::class);
        $anggota = Anggota::find($this->deletingId);

        if ($anggota) {
            $service->deleteFile($anggota->qr_code);
            if ($anggota->foto_dobok) {
                $service->deleteFile($anggota->foto_dobok);
            }
            $anggota->delete();
        }

        $this->closeDelete();
        $this->resetPage();
        $this->notify('Data anggota berhasil dihapus.');
    }

    public function viewingMember(): ?Anggota
    {
        return $this->viewingId ? Anggota::find($this->viewingId) : null;
    }

    public function deletingMember(): ?Anggota
    {
        return $this->deletingId ? Anggota::find($this->deletingId) : null;
    }

    /**
     * Tandai data yang belum lengkap (hasil import dsb.) agar ditampilkan badge di tabel.
     */
    public function isIncomplete(Anggota $member): bool
    {
        $prodi = trim((string) $member->program_studi);
        $wa = (string) $member->no_whatsapp;

        $prodiValid = $prodi !== '' && $prodi !== '-';
        $waValid = preg_match('/^62\d{8,13}$/', $wa) === 1;

        return ! $prodiValid || ! $waValid;
    }

    #[On('exportAnggota')]
    public function export()
    {
        return Excel::download(
            new DatabaseAnggotaExport($this->search, $this->fakultasFilter, $this->prodiFilter),
            'Database_Anggota_UKM.xlsx'
        );
    }

    #[On('importAnggota')]
    public function openImport(): void
    {
        $this->showImport = true;
        $this->importFile = null;
    }

    public function closeImport(): void
    {
        $this->showImport = false;
        $this->importFile = null;
    }

    public function import(): void
    {
        $this->validate([
            'importFile' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        $extension = $this->importFile->getClientOriginalExtension() ?: 'xlsx';
        $path = $this->importFile->storeAs('imports', 'anggota-'.now()->timestamp.'.'.$extension, 'local');

        try {
            $import = new AnggotaImport;
            Excel::import($import, $path, 'local');

            $this->notify('Import selesai: '.$import->getImported().' anggota ditambahkan, '.$import->getSkipped().' dilewati.');
        } catch (\Throwable $e) {
            $this->notify('Import gagal: '.$e->getMessage(), 'Gagal', 'error');
        } finally {
            File::delete(storage_path('app/private/'.$path));
        }

        $this->closeImport();
        $this->resetPage();
    }

    private function notify(string $message, string $title = 'Berhasil', string $type = 'success'): void
    {
        $this->dispatch('toast', title: $title, message: $message, type: $type);
        $this->dispatch('notify', message: $message, type: $type);
    }

    private function resetForm(): void
    {
        $this->nama = '';
        $this->nim = '';
        $this->tglLahir = '';
        $this->jk = '';
        $this->wa = '';
        $this->fakultas = '';
        $this->prodi = '';
        $this->bpjs = '';
        $this->status_anggota = Anggota::STATUS_AKTIF;
    }

    public function render()
    {
        return view('livewire.daftar-anggota', [
            'members' => $this->filteredQuery()->orderBy('id_anggota')->paginate($this->perPage),
            'total' => $this->filteredQuery()->count(),
        ]);
    }
}
