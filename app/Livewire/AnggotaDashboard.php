<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\Jadwal;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AnggotaDashboard extends Component
{
    public array $anggota = [];

    public array $statistik = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0, 'total_sesi' => 0];

    public ?array $liburTerdekat = null;

    public ?array $jadwalTerdekat = null;

    public array $riwayat = [];

    public array $bulanList = [];

    public string $bulanTerpilih = '';

    public function mount(): void
    {
        $this->loadData();
    }

    /**
     * Dipanggil oleh wire:poll agar statistik, jadwal, dan riwayat selalu segar.
     */
    public function refreshData(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        Carbon::setLocale('id');

        $anggotaModel = auth()->user()?->anggota;

        $this->anggota = $anggotaModel
            ? [
                'nama' => $anggotaModel->nama_lengkap,
                'id_anggota' => $anggotaModel->id_anggota,
                'no_bpjs' => $anggotaModel->no_bpjs ?? '-',
                'foto' => $anggotaModel->foto_dobok,
                'qr_code' => $anggotaModel->qr_code,
                'inisial' => strtoupper(mb_substr($anggotaModel->nama_lengkap, 0, 1)),
                'status' => $anggotaModel->status_anggota,
            ]
            : [
                'nama' => 'Anggota',
                'id_anggota' => '-',
                'no_bpjs' => '-',
                'foto' => null,
                'qr_code' => null,
                'inisial' => '?',
                'status' => Anggota::STATUS_AKTIF,
            ];

        [$statistik, $riwayat, $bulanList] = $this->rekapPresensi($anggotaModel?->id);

        $this->statistik = $statistik;
        $this->riwayat = $riwayat;
        $this->bulanList = $bulanList;

        $previousBulan = $this->bulanTerpilih;
        $this->bulanTerpilih = array_key_exists($previousBulan, $bulanList)
            ? $previousBulan
            : (array_key_first($bulanList) ?? '');

        $liburTerdekat = HariLibur::query()
            ->whereDate('tanggal', '>=', Carbon::today()->toDateString())
            ->whereDate('tanggal', '<=', Carbon::today()->endOfWeek(Carbon::SUNDAY)->toDateString())
            ->orderBy('tanggal')
            ->first();

        $this->liburTerdekat = $liburTerdekat
            ? [
                'tanggal' => Carbon::parse($liburTerdekat->tanggal)->translatedFormat('l, d F Y'),
                'keterangan' => $liburTerdekat->keterangan,
            ]
            : null;

        $this->jadwalTerdekat = $this->jadwalTerdekat();
    }

    /**
     * Sesi latihan berikutnya berdasarkan jadwal mingguan, dengan tanggal libur dilewati.
     */
    private function jadwalTerdekat(): ?array
    {
        $jadwalPerHari = Jadwal::query()
            ->orderBy('jam_start')
            ->get()
            ->groupBy('hari');

        if ($jadwalPerHari->isEmpty()) {
            return null;
        }

        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $batasPencarian = $today->copy()->addDays(366);

        $tanggalLibur = HariLibur::query()
            ->whereBetween('tanggal', [$today->toDateString(), $batasPencarian->toDateString()])
            ->pluck('tanggal')
            ->map(fn ($tanggal) => Carbon::parse($tanggal)->toDateString())
            ->flip()
            ->all();

        for ($offset = 0; $offset <= 366; $offset++) {
            $tanggal = $today->copy()->addDays($offset);
            $namaHari = $tanggal->copy()->locale('id')->translatedFormat('l');
            $jadwalHari = $jadwalPerHari->get($namaHari, collect());

            if ($jadwalHari->isEmpty()) {
                continue;
            }

            if ($offset === 0) {
                $jadwalHari = $jadwalHari
                    ->filter(fn ($jadwal) => $tanggal->copy()
                        ->setTimeFromTimeString($jadwal->jam_start)
                        ->gte($now))
                    ->values();

                if ($jadwalHari->isEmpty()) {
                    continue;
                }
            }

            if (isset($tanggalLibur[$tanggal->toDateString()])) {
                continue;
            }

            $jadwal = $jadwalHari->first();

            return [
                'tanggal' => $tanggal->copy()->locale('id')->translatedFormat('l, d F Y'),
                'jam' => $jadwal->jam_start.' WIB',
            ];
        }

        return null;
    }

    /**
     * Ringkasan statistik + riwayat presensi anggota dari tabel absensi.
     */
    private function rekapPresensi(?int $anggotaId): array
    {
        $statistik = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0, 'total_sesi' => 0];
        $riwayat = [];
        $bulanList = [];

        if (! $anggotaId) {
            return [$statistik, $riwayat, $bulanList];
        }

        $absensi = Absensi::query()
            ->where('anggota_id', $anggotaId)
            ->orderByDesc('tanggal')
            ->get();

        foreach ($absensi as $a) {
            $tanggal = Carbon::parse($a->tanggal);
            $statistik[$a->status] ??= 0;
            $statistik[$a->status]++;
            $statistik['total_sesi']++;

            $bulan = $tanggal->format('m-Y');
            $bulanList[$bulan] = $tanggal->translatedFormat('F Y');

            $riwayat[] = [
                'day' => $tanggal->translatedFormat('l'),
                'date' => $tanggal->format('d M'),
                'status' => $a->status,
                'bulan' => $bulan,
            ];
        }

        return [$statistik, array_slice($riwayat, 0, 10), $bulanList];
    }

    public function render()
    {
        return view('livewire.anggota-dashboard');
    }
}
