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

    public string $start;

    public string $end;

    public int $perPage = 10;

    public function mount(): void
    {
        $this->start = Carbon::now()->startOfMonth()->toDateString();
        $this->end = Carbon::now()->endOfMonth()->toDateString();
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
        $this->reset(['search']);
        $this->start = Carbon::now()->startOfMonth()->toDateString();
        $this->end = Carbon::now()->endOfMonth()->toDateString();
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
        $search = strtolower(trim($this->search));

        return Anggota::query()
            ->withCount([
                'absensi as total_hadir' => fn ($q) => $q->where('status', Absensi::STATUS_HADIR)->whereDate('tanggal', '>=', $this->start)->whereDate('tanggal', '<=', $this->end),
                'absensi as total_sakit' => fn ($q) => $q->where('status', Absensi::STATUS_SAKIT)->whereDate('tanggal', '>=', $this->start)->whereDate('tanggal', '<=', $this->end),
                'absensi as total_izin' => fn ($q) => $q->where('status', Absensi::STATUS_IZIN)->whereDate('tanggal', '>=', $this->start)->whereDate('tanggal', '<=', $this->end),
                'absensi as total_alfa' => fn ($q) => $q->where('status', Absensi::STATUS_ALFA)->whereDate('tanggal', '>=', $this->start)->whereDate('tanggal', '<=', $this->end),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(nama_lengkap) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(nim) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(id_anggota) LIKE ?', ["%{$search}%"]);
                });
            });
    }

    public function summary(): array
    {
        $counts = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];

        $rows = Absensi::query()
            ->whereDate('tanggal', '>=', $this->start)
            ->whereDate('tanggal', '<=', $this->end)
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
        return Excel::download(
            new RekapKehadiranExport($this->search, $this->start, $this->end),
            'Rekap_Kehadiran_'.$this->start.'_sd_'.$this->end.'.xlsx'
        );
    }

    public function exportPdf()
    {
        $anggota = $this->anggotaQuery()->orderBy('id_anggota')->get();
        $summary = $this->summary();
        $settings = PengaturanProfil::instance();

        $html = view('pdf.rekap-kehadiran', [
            'anggota' => $anggota,
            'summary' => $summary,
            'start' => $this->start,
            'end' => $this->end,
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
        }, 'Rekap_Kehadiran_'.$this->start.'_sd_'.$this->end.'.pdf', ['Content-Type' => 'application/pdf']);
    }

    public function render()
    {
        return view('livewire.rekap-kehadiran', [
            'anggota' => $this->anggotaQuery()->orderBy('id_anggota')->paginate($this->perPage),
            'summary' => $this->summary(),
        ]);
    }
}
