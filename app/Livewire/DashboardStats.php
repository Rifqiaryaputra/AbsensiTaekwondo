<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\PengaturanProfil;
use App\Models\User;
use App\Services\JadwalService;
use Dompdf\Dompdf;
use Illuminate\Support\Carbon;
use Livewire\Component;

class DashboardStats extends Component
{
    public array $kpis = [];

    public array $gender = [];

    public array $kehadiranHariIni = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];

    public array $anggotaAktif = [];

    public array $seringAlfa = [];

    public string $bulan = '';

    public function mount(): void
    {
        $this->bulan = now()->format('Y-m');
        $this->refresh();
    }

    public function updatedBulan(): void
    {
        // Chart dirender ulang via morph.updated; tidak ada logika tambahan.
    }

    public function refresh(): void
    {
        $now = Carbon::now();

        $totalAnggota = Anggota::count();
        $totalPetugas = User::query()->where('role', User::ROLE_PETUGAS)->count();
        $totalLibur = HariLibur::count();

        $kehadiranHariIni = Absensi::query()
            ->whereDate('tanggal', $now->toDateString())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $this->kehadiranHariIni = [
            'hadir' => $kehadiranHariIni['hadir'] ?? 0,
            'izin' => $kehadiranHariIni['izin'] ?? 0,
            'sakit' => $kehadiranHariIni['sakit'] ?? 0,
            'alfa' => $kehadiranHariIni['alfa'] ?? 0,
        ];

        $laki = Anggota::query()->where('jenis_kelamin', 'L')->count();
        $perempuan = Anggota::query()->where('jenis_kelamin', 'P')->count();
        $totalGender = $laki + $perempuan;

        $this->gender = [
            'total' => $totalGender,
            'laki' => ['jumlah' => $laki, 'persen' => $totalGender > 0 ? round($laki / $totalGender * 100) : 0],
            'perempuan' => ['jumlah' => $perempuan, 'persen' => $totalGender > 0 ? round($perempuan / $totalGender * 100) : 0],
        ];

        $this->kpis = [
            ['label' => 'TOTAL ANGGOTA', 'value' => $totalAnggota, 'icon' => 'group', 'bg' => 'bg-brand-light dark:bg-brand-blue/20', 'text' => 'text-brand-blue'],
            ['label' => 'TOTAL PETUGAS', 'value' => $totalPetugas, 'icon' => 'shield_person', 'bg' => 'bg-purple-100 dark:bg-purple-900/50', 'text' => 'text-purple-600 dark:text-purple-300'],
            ['label' => 'HARI LIBUR', 'value' => $totalLibur, 'icon' => 'calendar_today', 'bg' => 'bg-orange-100 dark:bg-orange-900/50', 'text' => 'text-orange-500 dark:text-orange-300'],
            ['label' => 'HADIR HARI INI', 'value' => $this->kehadiranHariIni['hadir'], 'icon' => 'check_circle', 'bg' => 'bg-green-50 dark:bg-green-900/30', 'text' => 'text-green-500 dark:text-green-400'],
        ];

        $this->anggotaAktif = $this->topBerdasarkanStatus(Absensi::STATUS_HADIR, $now, 3);
        $this->seringAlfa = $this->topBerdasarkanStatus(Absensi::STATUS_ALFA, $now, 3);
    }

    private function topBerdasarkanStatus(string $status, Carbon $now, int $limit): array
    {
        $rows = Absensi::query()
            ->where('status', $status)
            ->whereBetween('tanggal', [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()])
            ->selectRaw('anggota_id, count(*) as total')
            ->groupBy('anggota_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $anggota = $row->anggota;
            if (! $anggota) {
                continue;
            }
            $result[] = [
                'nama' => $anggota->nama_lengkap,
                'nim' => $anggota->nim,
                'inisial' => strtoupper(mb_substr($anggota->nama_lengkap, 0, 1)),
                'foto' => $anggota->foto_dobok,
                $status === Absensi::STATUS_ALFA ? 'alfa' : 'kehadiran' => $row->total,
            ];
        }

        return $result;
    }

    public function render()
    {
        return view('livewire.dashboard-stats', [
            'chartData' => $this->chartData(),
            'bulanOptions' => $this->bulanOptions(),
        ]);
    }

    /**
     * Data grouped bar chart: total Hadir/Izin/Sakit/Alfa per tanggal latihan pada bulan terpilih.
     * Label sumbu-X diformat "Sen, 3 Agu" (hari + tanggal + bulan, lokal id).
     */
    public function chartData(): array
    {
        \Carbon\Carbon::setLocale('id');

        $start = \Carbon\Carbon::parse($this->bulan.'-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $rows = Absensi::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('tanggal, status, count(*) as total')
            ->groupBy('tanggal', 'status')
            ->orderBy('tanggal')
            ->get();

        $perDate = $rows->groupBy(fn ($row) => $row->tanggal->format('Y-m-d'));

        $shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $labels = [];
        $series = ['hadir' => [], 'izin' => [], 'sakit' => [], 'alfa' => []];

        foreach ($perDate as $dateStr => $dateRows) {
            $date = \Carbon\Carbon::parse($dateStr);
            $labels[] = $date->translatedFormat('D, j').' '.$shortMonths[$date->month - 1];

            $counts = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];
            foreach ($dateRows as $row) {
                if (isset($counts[$row->status])) {
                    $counts[$row->status] = $row->total;
                }
            }

            foreach (['hadir', 'izin', 'sakit', 'alfa'] as $status) {
                $series[$status][] = $counts[$status];
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Hadir', 'data' => $series['hadir'], 'backgroundColor' => '#22c55e'],
                ['label' => 'Izin', 'data' => $series['izin'], 'backgroundColor' => '#3b82f6'],
                ['label' => 'Sakit', 'data' => $series['sakit'], 'backgroundColor' => '#eab308'],
                ['label' => 'Alfa', 'data' => $series['alfa'], 'backgroundColor' => '#ef4444'],
            ],
        ];
    }

    /**
     * Opsi bulan dropdown: dari bulan data terawal (absensi/jadwal) hingga bulan berjalan.
     */
    public function bulanOptions(): array
    {
        $earliest = Absensi::query()->min('tanggal')
            ?? Jadwal::query()->min('created_at');

        $start = $earliest ? \Carbon\Carbon::parse($earliest)->startOfMonth() : now()->startOfMonth();
        $end = now()->startOfMonth();

        $options = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $options[$cursor->format('Y-m')] = $cursor->translatedFormat('F Y');
            $cursor->addMonth();
        }

        return $options;
    }

    public function exportLaporan()
    {
        $settings = PengaturanProfil::instance();
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        // Total sesi latihan bulan ini (tidak termasuk tanggal hari libur).
        $totalLatihan = $this->totalLatihanBulanIni($start, $end);

        $totalLibur = HariLibur::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->count();

        $topTerajin = $this->topPerStatus(Absensi::STATUS_HADIR, $start, $end, 3);
        $topAlfa = $this->topPerStatus(Absensi::STATUS_ALFA, $start, $end, 5);

        $bulanLabel = $start->translatedFormat('F Y');

        $html = view('pdf.laporan-dashboard', compact('settings', 'start', 'end', 'totalLatihan', 'totalLibur', 'topTerajin', 'topAlfa', 'bulanLabel'))->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        return response()->streamDownload(function () use ($output) {
            echo $output;
        }, 'Laporan_Bulan_Ini.pdf', ['Content-Type' => 'application/pdf']);
    }

    /**
     * Jumlah sesi latihan dalam rentang tanggal, mengecualikan hari libur yang bertepatan.
     */
    public function totalLatihanBulanIni(\Carbon\Carbon $start, \Carbon\Carbon $end): int
    {
        $jadwalService = app(JadwalService::class);

        $holidayDates = HariLibur::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->pluck('tanggal')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->toArray();

        $total = 0;
        foreach (Jadwal::all() as $jadwal) {
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                if (! in_array($cursor->toDateString(), $holidayDates, true)
                    && $jadwalService->hariNama($cursor) === $jadwal->hari) {
                    $total++;
                }
                $cursor->addDay();
            }
        }

        return $total;
    }

    /**
     * @return array<int, array{nama: string, nim: string, total: int}>
     */
    private function topPerStatus(string $status, Carbon $start, Carbon $end, int $limit): array
    {
        return Absensi::query()
            ->where('status', $status)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('anggota_id, count(*) as total')
            ->groupBy('anggota_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $anggota = $row->anggota;

                return $anggota
                    ? ['nama' => $anggota->nama_lengkap, 'nim' => $anggota->nim, 'total' => $row->total]
                    : null;
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
