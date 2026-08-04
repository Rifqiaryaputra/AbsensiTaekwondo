<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\HariLibur;
use App\Models\Jadwal;
use Illuminate\Support\Carbon;

class AnggotaDashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        $user = auth()->user();
        $anggotaModel = $user?->anggota;

        $anggota = $anggotaModel
            ? [
                'nama' => $anggotaModel->nama_lengkap,
                'id_anggota' => $anggotaModel->id_anggota,
                'no_bpjs' => $anggotaModel->no_bpjs ?? '-',
                'foto' => $anggotaModel->foto_dobok,
                'qr_code' => $anggotaModel->qr_code,
                'inisial' => strtoupper(mb_substr($anggotaModel->nama_lengkap, 0, 1)),
            ]
            : [
                'nama' => 'Anggota',
                'id_anggota' => '-',
                'no_bpjs' => '-',
                'foto' => null,
                'qr_code' => null,
                'inisial' => '?',
            ];

        [$statistik, $riwayat, $bulanList] = $this->rekapPresensi($anggotaModel?->id);

        $liburTerdekat = HariLibur::query()
            ->whereDate('tanggal', '>=', Carbon::today()->toDateString())
            ->orderBy('tanggal')
            ->first();

        $liburTerdekat = $liburTerdekat
            ? [
                'tanggal' => Carbon::parse($liburTerdekat->tanggal)->translatedFormat('l, d F Y'),
                'keterangan' => $liburTerdekat->keterangan,
            ]
            : null;

        $jadwalTerdekat = $this->jadwalTerdekat();

        $bulanTerpilih = array_key_first($bulanList) ?? '';

        return view('anggota.dashboard', compact('anggota', 'statistik', 'liburTerdekat', 'jadwalTerdekat', 'riwayat', 'bulanList', 'bulanTerpilih'));
    }

    /**
     * Sesi latihan berikutnya berdasarkan jadwal mingguan (hari + jam terdekat dari hari ini).
     */
    private function jadwalTerdekat(): ?array
    {
        $hariKe = [
            'Minggu' => 0,
            'Senin' => 1,
            'Selasa' => 2,
            'Rabu' => 3,
            'Kamis' => 4,
            'Jumat' => 5,
            'Sabtu' => 6,
        ];

        $today = Carbon::today();
        $terdekat = null;
        $result = null;

        foreach (Jadwal::query()->orderBy('jam_start')->get() as $jadwal) {
            $next = $today->copy()->next((int) $hariKe[$jadwal->hari]);

            if ($terdekat === null || $next->lt($terdekat)) {
                $terdekat = $next;
                $result = [
                    'tanggal' => $next->translatedFormat('l, d F Y'),
                    'jam' => $jadwal->jam_start.' WIB',
                ];
            }
        }

        return $result;
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
}
