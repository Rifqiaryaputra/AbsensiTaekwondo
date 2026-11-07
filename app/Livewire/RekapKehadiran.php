<?php

namespace App\Livewire;

use App\Exports\RekapKehadiranExport;
use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\PengaturanProfil;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class RekapKehadiran extends Component
{
    use WithPagination;

    public string $search = '';

    public string $start = '';

    public string $end = '';

    public int $perPage = 10;

    public bool $showEditModal = false;

    public ?Anggota $selectedAnggota = null;

    public array $memberAttendanceDetails = [];

    public function getEffectiveDates(): array
    {
        if ($this->start !== '' && $this->end !== '') {
            return ['start' => $this->start, 'end' => $this->end];
        }

        $latest = Absensi::query()->whereDate('tanggal', '<=', now()->toDateString())->max('tanggal');
        $target = $latest ? Carbon::parse($latest)->toDateString() : Carbon::now()->toDateString();

        return ['start' => $target, 'end' => $target];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStart(): void
    {
        $this->normalizeRange();
        $this->resetPage();
    }

    public function updatedEnd(): void
    {
        $this->normalizeRange();
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'start', 'end']);
        $this->resetPage();
    }

    private function normalizeRange(): void
    {
        if ($this->start !== '' && $this->end !== '' && $this->start > $this->end) {
            $this->start = $this->end;
        }
    }

    /**
     * Rekap agregat per anggota: total Hadir/Sakit/Izin/Alfa pada rentang tanggal.
     */
    public function anggotaQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $searchTerms = collect(explode(',', $this->search))
            ->map(fn ($term) => strtolower(trim($term)))
            ->filter()
            ->values()
            ->all();

        [$start, $end] = array_values($this->getEffectiveDates());

        return Anggota::query()
            ->where('status_anggota', 'aktif')
            ->withCount([
                'absensi as total_hadir' => fn ($q) => $q->where('status', Absensi::STATUS_HADIR)->whereDate('tanggal', '>=', $start)->whereDate('tanggal', '<=', $end),
                'absensi as total_sakit' => fn ($q) => $q->where('status', Absensi::STATUS_SAKIT)->whereDate('tanggal', '>=', $start)->whereDate('tanggal', '<=', $end),
                'absensi as total_izin' => fn ($q) => $q->where('status', Absensi::STATUS_IZIN)->whereDate('tanggal', '>=', $start)->whereDate('tanggal', '<=', $end),
                'absensi as total_alfa' => fn ($q) => $q->where('status', Absensi::STATUS_ALFA)->whereDate('tanggal', '>=', $start)->whereDate('tanggal', '<=', $end),
            ])
            ->when($searchTerms !== [], function ($query) use ($searchTerms) {
                $query->where(function ($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->orWhereRaw('LOWER(nama_lengkap) LIKE ?', ["%{$term}%"])
                            ->orWhereRaw('LOWER(nim) LIKE ?', ["%{$term}%"])
                            ->orWhereRaw('LOWER(id_anggota) LIKE ?', ["%{$term}%"]);
                    }
                });
            });
    }

    public function summary(): array
    {
        $counts = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];

        [$start, $end] = array_values($this->getEffectiveDates());

        $rows = Absensi::query()
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $counts['hadir'] = $rows['hadir'] ?? 0;
        $counts['izin'] = $rows['izin'] ?? 0;
        $counts['sakit'] = $rows['sakit'] ?? 0;
        $counts['alfa'] = $rows['alfa'] ?? 0;

        return $counts;
    }

    public function formatShort(string $date): string
    {
        return Carbon::parse($date)->translatedFormat('d M Y');
    }

    public function exportExcel()
    {
        [$start, $end] = array_values($this->getEffectiveDates());

        return Excel::download(
            new RekapKehadiranExport($this->search, $start, $end),
            'Rekap_Kehadiran_'.$start.'_sd_'.$end.'.xlsx'
        );
    }

    public function exportPdf()
    {
        [$start, $end] = array_values($this->getEffectiveDates());
        $anggota = $this->anggotaQuery()->orderBy('id_anggota')->get();
        $summary = $this->summary();
        $settings = PengaturanProfil::instance();

        $html = view('pdf.rekap-kehadiran', [
            'anggota' => $anggota,
            'summary' => $summary,
            'start' => $start,
            'end' => $end,
            'search' => $this->search,
            'settings' => $settings,
        ])->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $output = $dompdf->output();

        return response()->streamDownload(function () use ($output) {
            echo $output;
        }, 'Rekap_Kehadiran_'.$start.'_sd_'.$end.'.pdf', ['Content-Type' => 'application/pdf']);
    }

    public function render()
    {
        return view('livewire.rekap-kehadiran', [
            'anggota' => $this->anggotaQuery()->orderBy('id_anggota')->paginate($this->perPage),
            'summary' => $this->summary(),
            'dates' => $this->getEffectiveDates(),
        ]);
    }

    public function openEditModal($anggotaId): void
    {
        abort_if(! auth()->check() || auth()->user()->role !== 'admin', 403, 'Akses ditolak.');

        [$start, $end] = array_values($this->getEffectiveDates());

        $this->selectedAnggota = Anggota::find($anggotaId);

        if (! $this->selectedAnggota) {
            return;
        }

        $this->memberAttendanceDetails = Absensi::query()
            ->where('anggota_id', $this->selectedAnggota->id)
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->orderBy('tanggal')
            ->get()
            ->map(fn (Absensi $absensi) => [
                'id' => $absensi->id,
                'tanggal' => $absensi->tanggal->toDateString(),
                'status' => $absensi->status,
            ])
            ->values()
            ->all();

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->selectedAnggota = null;
        $this->memberAttendanceDetails = [];
    }

    public function updateStatus($absensiId, string $newStatus): void
    {
        abort_if(! auth()->check() || auth()->user()->role !== 'admin', 403, 'Akses ditolak.');

        $valid = [Absensi::STATUS_HADIR, Absensi::STATUS_IZIN, Absensi::STATUS_SAKIT, Absensi::STATUS_ALFA];

        if (! in_array($newStatus, $valid, true)) {
            return;
        }

        $absensi = Absensi::find($absensiId);

        if (! $absensi) {
            return;
        }

        $absensi->update(['status' => $newStatus]);

        $this->dispatch('toast', title: 'Status Diperbarui', message: 'Status kehadiran berhasil diubah.', type: 'success');

        $this->openEditModal($this->selectedAnggota->id);
    }
}
