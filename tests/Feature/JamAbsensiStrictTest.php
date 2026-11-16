<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\Jadwal;
use App\Models\User;
use App\Services\AbsensiService;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsFakultasProdi;
use Tests\TestCase;

class JamAbsensiStrictTest extends TestCase
{
    use RefreshDatabase;
    use SeedsFakultasProdi;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function siapkanSesi(string $waktu): array
    {
        Carbon::setTestNow(Carbon::parse($waktu, JadwalService::TIMEZONE));

        $jadwal = Jadwal::create([
            'hari' => app(JadwalService::class)->hariNama(Carbon::now(JadwalService::TIMEZONE)),
            'jam_start' => '16:00:00',
            'jam_close' => '18:00:00',
        ]);

        [$fakultas, $prodi] = $this->fakultasProdi('FMIPA', 'Matematika');

        $anggota = Anggota::create([
            'id_anggota' => 'TKD-JAM-'.str_replace(':', '', $waktu),
            'nama_lengkap' => 'Anggota Uji Jam',
            'nim' => '2211'.str_replace(':', '', substr($waktu, 11)),
            'tanggal_lahir' => '2003-01-01',
            'jenis_kelamin' => 'L',
            'no_whatsapp' => '08123',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'qr_code' => 'qr-codes/test.svg',
        ]);

        return [$jadwal, $anggota, User::factory()->create()];
    }

    private function catat(Anggota $anggota, string $waktu, User $petugas): array
    {
        Carbon::setTestNow(Carbon::parse($waktu, JadwalService::TIMEZONE));

        return app(AbsensiService::class)->catatKehadiran(
            $anggota,
            Absensi::SUMBER_MANUAL,
            $petugas->id,
            Carbon::now(JadwalService::TIMEZONE)
        );
    }

    public function test_ditolak_satu_detik_sebelum_jam_mulai_dengan_info_jam_sistem(): void
    {
        [, $anggota, $petugas] = $this->siapkanSesi('2026-11-17 15:59:59');

        $hasil = $this->catat($anggota, '2026-11-17 15:59:59', $petugas);

        $this->assertFalse($hasil['ok']);
        $this->assertStringContainsString('Bukan jam absen', $hasil['message']);
        $this->assertStringContainsString('16:00 - 18:00 WIB', $hasil['message']);
        $this->assertStringContainsString('Jam sistem: 15:59:59 WIB', $hasil['message']);
        $this->assertDatabaseCount('absensi', 0);
    }

    public function test_diterima_tepat_pada_jam_mulai(): void
    {
        [, $anggota, $petugas] = $this->siapkanSesi('2026-11-17 16:00:00');

        $hasil = $this->catat($anggota, '2026-11-17 16:00:00', $petugas);

        $this->assertTrue($hasil['ok']);
        $this->assertDatabaseCount('absensi', 1);
    }

    public function test_diterima_tepat_pada_jam_selesai(): void
    {
        [, $anggota, $petugas] = $this->siapkanSesi('2026-11-17 18:00:00');

        $hasil = $this->catat($anggota, '2026-11-17 18:00:00', $petugas);

        $this->assertTrue($hasil['ok']);
        $this->assertDatabaseCount('absensi', 1);
    }

    public function test_ditolak_setelah_jam_selesai(): void
    {
        [, $anggota, $petugas] = $this->siapkanSesi('2026-11-17 18:00:01');

        $hasil = $this->catat($anggota, '2026-11-17 18:00:01', $petugas);

        $this->assertFalse($hasil['ok']);
        $this->assertStringContainsString('Jam sistem: 18:00:01 WIB', $hasil['message']);
        $this->assertDatabaseCount('absensi', 0);
    }
}
