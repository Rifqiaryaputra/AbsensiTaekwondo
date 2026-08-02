<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Support\Carbon;

class AnggotaDashboardController extends Controller
{
    public function index()
    {
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
                'nama' => 'Budi Santoso',
                'id_anggota' => 'TKD25-013',
                'no_bpjs' => '26014267913',
                'foto' => null,
                'qr_code' => null,
                'inisial' => 'BS',
            ];

        [$statistik, $riwayat, $bulanList] = $this->rekapPresensi($anggotaModel?->id);

        $liburTerdekat = [
            'tanggal' => 'Senin, 17 Agustus 2026',
            'keterangan' => 'Kemerdekaan RI',
        ];

        $jadwalTerdekat = [
            'tanggal' => 'Jumat, 31 Jul 2026',
            'jam' => '15:30 WIB',
            'lokasi' => 'Lap. Utama',
        ];

        $bulanTerpilih = array_key_first($bulanList) ?? '';

        return view('anggota.dashboard', compact('anggota', 'statistik', 'liburTerdekat', 'jadwalTerdekat', 'riwayat', 'bulanList', 'bulanTerpilih'));
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
