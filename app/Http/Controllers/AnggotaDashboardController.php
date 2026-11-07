<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Services\AnggotaService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

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

        $liburTerdekat = HariLibur::query()
            ->whereDate('tanggal', '>=', Carbon::today()->toDateString())
            ->whereDate('tanggal', '<=', Carbon::today()->endOfWeek(Carbon::SUNDAY)->toDateString())
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
     * Unduh QR Code keanggotaan member yang login (hanya QR, tanpa kartu).
     */
    public function downloadQR()
    {
        $anggota = auth()->user()?->anggota;

        abort_unless($anggota, 404);

        if (! $anggota->qr_code || ! File::exists(public_path($anggota->qr_code))) {
            $anggota->qr_code = app(AnggotaService::class)->generateQrCode($anggota->id_anggota);
            $anggota->save();
        }

        return response()->download(public_path($anggota->qr_code), 'QR_'.$anggota->id_anggota.'.svg');
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
}
