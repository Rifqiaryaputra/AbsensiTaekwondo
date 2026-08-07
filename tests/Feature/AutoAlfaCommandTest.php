<?php

namespace Tests\Feature;

use App\Console\Commands\AutoAlfa;
use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\IzinSakit;
use App\Models\Jadwal;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoAlfaCommandTest extends TestCase
{
    use RefreshDatabase;

    private function hariIni(): string
    {
        return app(JadwalService::class)->hariNama(Carbon::now());
    }

    private function jadwalSelesai(): Jadwal
    {
        $now = Carbon::now();
        $start = $now->copy()->subMinutes(30)->format('H:i:s');
        $close = $now->copy()->subMinute()->format('H:i:s');
        if ($now->copy()->subMinutes(30)->isYesterday()) {
            $start = '00:00:00';
        }
        if ($now->copy()->subMinute()->isYesterday()) {
            $close = '00:00:00';
        }

        return Jadwal::create([
            'hari' => $this->hariIni(),
            'jam_start' => $start,
            'jam_close' => $close,
        ]);
    }

    private function anggota(string $nim, string $nama): Anggota
    {
        return Anggota::create([
            'id_anggota' => 'TKD'.substr($nim, 0, 2).'-'.substr($nim, -3),
            'nama_lengkap' => $nama,
            'nim' => $nim,
            'tanggal_lahir' => '2003-01-01',
            'jenis_kelamin' => 'L',
            'no_whatsapp' => '08123',
            'fakultas' => 'FMIPA',
            'program_studi' => 'Matematika',
            'qr_code' => 'qr-codes/test.svg',
        ]);
    }

    public function test_auto_alfa_marks_only_absentees(): void
    {
        $jadwal = $this->jadwalSelesai();
        $a = $this->anggota('221111201', 'Anggota Alfa');
        $b = $this->anggota('221111202', 'Anggota Hadir');
        $c = $this->anggota('221111203', 'Anggota Izin');

        Absensi::create([
            'anggota_id' => $b->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::now()->toDateString(),
            'status' => 'hadir',
            'sumber' => 'scan',
            'waktu_scan' => now(),
        ]);

        $izin = IzinSakit::create([
            'anggota_id' => $c->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::now()->toDateString(),
            'jenis' => 'izin',
            'keterangan' => 'uji',
            'status' => 'menunggu',
            'diajukan_pada' => now(),
        ]);
        $izin->update(['status' => 'disetujui', 'diproses_oleh' => null, 'diproses_pada' => now()]);

        $this->artisan(AutoAlfa::class)->assertSuccessful();

        $this->assertDatabaseHas('absensi', [
            'anggota_id' => $a->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'alfa',
            'sumber' => 'otomatis',
        ]);

        $this->assertDatabaseMissing('absensi', ['anggota_id' => $b->id, 'status' => 'alfa']);
        $this->assertDatabaseMissing('absensi', ['anggota_id' => $c->id, 'status' => 'alfa']);
        $this->assertDatabaseCount('absensi', 3); // hadir B, izin C, alfa A
    }

    public function test_auto_alfa_is_idempotent(): void
    {
        $jadwal = $this->jadwalSelesai();
        $a = $this->anggota('221111204', 'Anggota Idempotent');

        $this->artisan(AutoAlfa::class)->assertSuccessful();
        $this->artisan(AutoAlfa::class)->assertSuccessful();

        $this->assertDatabaseCount('absensi', 1);
        $this->assertDatabaseHas('absensi', ['anggota_id' => $a->id, 'status' => 'alfa']);
    }

    public function test_auto_alfa_ignores_non_aktif_and_alumni(): void
    {
        $jadwal = $this->jadwalSelesai();
        $aktif = $this->anggota('221111207', 'Anggota Tetap');
        $nonAktif = $this->anggota('221111208', 'Anggota Non Aktif');
        $alumni = $this->anggota('221111209', 'Anggota Alumni');

        $nonAktif->update(['status_anggota' => Anggota::STATUS_NON_AKTIF]);
        $alumni->update(['status_anggota' => Anggota::STATUS_ALUMNI]);

        $this->artisan(AutoAlfa::class)->assertSuccessful();

        $this->assertDatabaseHas('absensi', ['anggota_id' => $aktif->id, 'status' => 'alfa']);
        $this->assertDatabaseMissing('absensi', ['anggota_id' => $nonAktif->id]);
        $this->assertDatabaseMissing('absensi', ['anggota_id' => $alumni->id]);
        $this->assertDatabaseCount('absensi', 1);
    }

    public function test_auto_alfa_skips_hari_libur(): void
    {
        $this->jadwalSelesai();
        $this->anggota('221111205', 'Anggota LiburAlfa');
        HariLibur::create(['tanggal' => Carbon::now()->toDateString(), 'keterangan' => 'Uji']);

        $this->artisan(AutoAlfa::class)->assertSuccessful();

        $this->assertDatabaseCount('absensi', 0);
    }

    public function test_auto_alfa_noops_without_completed_jadwal(): void
    {
        $now = Carbon::now();
        $lainHari = $this->hariIni() === 'Minggu' ? 'Senin' : 'Minggu';
        Jadwal::create([
            'hari' => $lainHari,
            'jam_start' => $now->copy()->addHours(2)->format('H:i:s'),
            'jam_close' => $now->copy()->addHours(3)->format('H:i:s'),
        ]);
        $this->anggota('221111206', 'Anggota NoJadwalSelesai');

        $this->artisan(AutoAlfa::class)->assertSuccessful();

        $this->assertDatabaseCount('absensi', 0);
    }
}
