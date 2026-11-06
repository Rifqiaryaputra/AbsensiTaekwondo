<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Jadwal;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class KelolaHariLibur extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    /** @var array<int, int> */
    public array $editingIds = [];

    public string $tanggal_mulai = '';

    public string $tanggal_akhir = '';

    public string $keterangan = '';

    public bool $showDelete = false;

    /** @var array<int, int> */
    public array $deleteIds = [];

    public string $deleteTanggal = '';

    private array $datesToCheck = [];

    public function liburList(): Collection
    {
        return HariLibur::query()
            ->orderBy('tanggal')
            ->get();
    }

    /**
     * Gabungkan hari libur dengan keterangan sama dan tanggal berurutan menjadi satu baris.
     *
     * @return array<int, array{
     *     keterangan: string,
     *     tanggal_mulai: string,
     *     tanggal_akhir: string,
     *     tanggal_mulai_label: string,
     *     tanggal_akhir_label: string,
     *     tanggal_single_label: string,
     *     tanggal_label: string,
     *     is_range: bool,
     *     hari_latihan_valid: array<int, string>,
     *     ids: array<int, int>
     * }>
     */
    public function groupedLibur(): array
    {
        $libur = $this->liburList();

        if ($libur->isEmpty()) {
            return [];
        }

        $hariLatihan = Jadwal::query()->pluck('hari')->all();
        $groups = [];
        $current = null;
        $previousTanggal = null;

        foreach ($libur as $item) {
            if (! $item->tanggal) {
                continue;
            }

            $tanggal = $item->tanggal->copy()->startOfDay();
            $keterangan = (string) $item->keterangan;

            $sameGroup = $current !== null
                && $current['keterangan'] === $keterangan
                && $previousTanggal !== null
                && $previousTanggal->copy()->addDay()->isSameDay($tanggal);

            if (! $sameGroup) {
                if ($current !== null) {
                    $groups[] = $current;
                }

                $current = [
                    'keterangan' => $keterangan,
                    'tanggal_mulai' => $tanggal->toDateString(),
                    'tanggal_akhir' => $tanggal->toDateString(),
                    'hari_latihan_valid' => [],
                    'ids' => [],
                ];
            }

            $current['tanggal_akhir'] = $tanggal->toDateString();
            $current['ids'][] = (int) $item->id;

            $namaHari = $tanggal->copy()->locale('id')->translatedFormat('l');

            if (in_array($namaHari, $hariLatihan, true)) {
                $current['hari_latihan_valid'][] = $tanggal->copy()->locale('id')->translatedFormat('d M');
            }

            $previousTanggal = $tanggal;
        }

        if ($current !== null) {
            $groups[] = $current;
        }

        foreach ($groups as &$group) {
            $mulai = Carbon::parse($group['tanggal_mulai']);
            $akhir = Carbon::parse($group['tanggal_akhir']);

            $group['tanggal_mulai_label'] = $mulai->translatedFormat('d F Y');
            $group['tanggal_akhir_label'] = $akhir->translatedFormat('d F Y');
            $group['tanggal_single_label'] = $mulai->translatedFormat('l, d F Y');
            $group['is_range'] = ! $mulai->isSameDay($akhir);
            $group['tanggal_label'] = $group['is_range']
                ? $group['tanggal_mulai_label'].' - '.$group['tanggal_akhir_label']
                : $group['tanggal_single_label'];
        }

        unset($group);

        return $groups;
    }

    #[On('tambahLibur')]
    public function openForm(int|string|array $ids = 0): void
    {
        $this->showForm = true;
        $this->editingIds = $this->normalizeIds($ids);
        $this->editingId = $this->editingIds[0] ?? null;
        $this->tanggal_mulai = '';
        $this->tanggal_akhir = '';
        $this->keterangan = '';

        if ($this->editingIds !== []) {
            $libur = HariLibur::query()
                ->whereIn('id', $this->editingIds)
                ->orderBy('tanggal')
                ->get();

            if ($libur->isNotEmpty()) {
                $this->tanggal_mulai = $libur->first()->tanggal->format('Y-m-d');
                $this->tanggal_akhir = $libur->last()->tanggal->format('Y-m-d');
                $this->keterangan = (string) $libur->first()->keterangan;
            }
        }
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->editingIds = [];
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

        $this->tanggal_akhir = $this->tanggal_akhir ?: $this->tanggal_mulai;
        $editingIds = $this->editingIds;

        if ($this->editingId && $editingIds === []) {
            $editingIds = [$this->editingId];
        }

        $wasEditing = $editingIds !== [];
        $this->datesToCheck = [];

        foreach (CarbonPeriod::create($this->tanggal_mulai, $this->tanggal_akhir) as $date) {
            $this->datesToCheck[] = $date->format('Y-m-d');
        }

        $duplikat = HariLibur::query()
            ->whereIn('tanggal', $this->datesToCheck)
            ->when($wasEditing, fn ($q) => $q->whereNotIn('id', $editingIds))
            ->exists();

        if ($duplikat) {
            $this->dispatch('swal:error', title: 'Gagal Menyimpan', text: 'Satu atau beberapa tanggal dalam rentang tersebut sudah terinput sebagai hari libur.');

            return;
        }

        if ($wasEditing) {
            HariLibur::query()->whereIn('id', $editingIds)->delete();
            $this->editingId = null;
            $this->editingIds = [];
        }

        $this->saveRange();

        $this->closeForm();
        $this->dispatch('toast', title: 'Berhasil', message: $wasEditing ? 'Data libur berhasil diperbarui.' : 'Data libur berhasil ditambahkan.', type: 'success');
    }

    private function saveRange(): void
    {
        $duplikat = HariLibur::query()
            ->whereIn('tanggal', $this->datesToCheck)
            ->exists();

        if ($duplikat) {
            $this->dispatch('swal:error', title: 'Gagal Menyimpan', text: 'Satu atau beberapa tanggal dalam rentang tersebut sudah terinput sebagai hari libur.');

            return;
        }

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

    public function openDelete(int|string|array $ids = 0, ?string $tanggal = null): void
    {
        $this->deleteIds = $this->normalizeIds($ids);
        $this->deleteTanggal = $tanggal ?? $this->deleteLabel($this->deleteIds);
        $this->showDelete = $this->deleteIds !== [];
    }

    public function closeDelete(): void
    {
        $this->showDelete = false;
        $this->deleteIds = [];
        $this->deleteTanggal = '';
    }

    public function confirmDelete(): void
    {
        if ($this->deleteIds === []) {
            $this->closeDelete();

            return;
        }

        HariLibur::query()->whereIn('id', $this->deleteIds)->delete();

        $this->closeDelete();
        $this->dispatch('toast', title: 'Dihapus', message: 'Data libur berhasil dihapus.', type: 'success');
    }

    public function render()
    {
        return view('livewire.kelola-hari-libur', [
            'groupedLibur' => $this->groupedLibur(),
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function normalizeIds(int|string|array $ids): array
    {
        $ids = is_array($ids) ? $ids : [$ids];

        return collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, int>  $ids
     */
    private function deleteLabel(array $ids): string
    {
        $libur = HariLibur::query()
            ->whereIn('id', $ids)
            ->orderBy('tanggal')
            ->get();

        if ($libur->isEmpty() || ! $libur->first()->tanggal || ! $libur->last()->tanggal) {
            return '';
        }

        $mulai = $libur->first()->tanggal;
        $akhir = $libur->last()->tanggal;

        return $mulai->isSameDay($akhir)
            ? $mulai->translatedFormat('l, d F Y')
            : $mulai->translatedFormat('d F Y').' - '.$akhir->translatedFormat('d F Y');
    }
}
