<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;

class DashboardStats extends Component
{
    public array $kpis = [];

    public array $gender = [];

    public array $kehadiranHariIni = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];

    public array $anggotaAktif = [];

    public array $seringAlfa = [];

    public function mount(): void
    {
        $this->refresh();
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
            ['label' => 'TOTAL PETUGAS', 'value' => $totalPetugas, 'icon' => 'shield_person', 'bg' => 'bg-purple-50 dark:bg-purple-900/30', 'text' => 'text-purple-600 dark:text-purple-400'],
            ['label' => 'HARI LIBUR', 'value' => $totalLibur, 'icon' => 'calendar_today', 'bg' => 'bg-orange-50 dark:bg-orange-900/30', 'text' => 'text-orange-500 dark:text-orange-400'],
            ['label' => 'HADIR HARI INI', 'value' => $this->kehadiranHariIni['hadir'], 'icon' => 'check_circle', 'bg' => 'bg-green-50 dark:bg-green-900/30', 'text' => 'text-green-500 dark:text-green-400'],
        ];

        $this->anggotaAktif = $this->topBerdasarkanStatus(Absensi::STATUS_HADIR, $now, 2);
        $this->seringAlfa = $this->topBerdasarkanStatus(Absensi::STATUS_ALFA, $now, 2);
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
                $status === Absensi::STATUS_ALFA ? 'alfa' : 'kehadiran' => $row->total,
            ];
        }

        return $result;
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
