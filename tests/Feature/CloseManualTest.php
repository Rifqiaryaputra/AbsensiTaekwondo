<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\IzinSakit;
use App\Models\Jadwal;
use App\Models\User;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CloseManualTest extends TestCase
{
    use RefreshDatabase;

    private function hariIni(): string
    {
        return app(JadwalService::class)->hariNama(Carbon::now());
    }

    private function jadwalAktif(): Jadwal
    {
        $now = Carbon::now();
        $start = $now->copy()->subHour()->format('H:i:s');
        $end = $now->copy()->addHour()->format('H:i:s');
        if ($now->copy()->subHour()->isYesterday()) {
            $start = '00:00:00';
        }
        if ($now->copy()->addHour()->isTomorrow()) {
            $end = '23:59:59';
        }

        return Jadwal::create([
            'hari' => $this->hariIni(),
            'jam_start' => $start,
            'jam_close' => $end,
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

    private function petugas(): User
    {
        return User::factory()->create(['role' => User::ROLE_PETUGAS]);
    }

    public function test_close_manual_records_alfa_for_missing_anggota(): void
    {
        $this->actingAs($this->petugas());
        $jadwal = $this->jadwalAktif();
        $a = $this->anggota('221111301', 'Anggota Alfa');
        $b = $this->anggota('221111302', 'Anggota Hadir');
        $c = $this->anggota('221111303', 'Anggota Izin');

        Absensi::create([
            'anggota_id' => $b->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::now()->toDateString(),
            'status' => Absensi::STATUS_HADIR,
            'sumber' => Absensi::SUMBER_SCAN,
            'waktu_scan' => now(),
        ]);

        $izin = IzinSakit::create([
            'anggota_id' => $c->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::now()->toDateString(),
            'jenis' => IzinSakit::JENIS_IZIN,
            'keterangan' => 'uji',
            'status' => IzinSakit::STATUS_MENUNGGU,
            'diproses_oleh' => null,
            'diajukan_pada' => now(),
        ]);
        $izin->update(['status' => IzinSakit::STATUS_DISETUJUI, 'diproses_oleh' => null, 'diproses_pada' => now()]);

        $this->post(route('jadwal.tutup', $jadwal->id))
            ->assertRedirect()
            ->assertSessionHas('success', 'Sesi absensi ditutup. Anggota yang belum hadir direkap sebagai Alfa.');

        $this->assertDatabaseHas('absensi', [
            'anggota_id' => $a->id,
            'jadwal_id' => $jadwal->id,
            'status' => Absensi::STATUS_ALFA,
            'sumber' => Absensi::SUMBER_OTOMATIS,
        ]);
        $this->assertDatabaseMissing('absensi', ['anggota_id' => $b->id, 'status' => Absensi::STATUS_ALFA]);
        $this->assertDatabaseMissing('absensi', ['anggota_id' => $c->id, 'status' => Absensi::STATUS_ALFA]);
        $this->assertDatabaseCount('absensi', 3);
        $this->assertTrue($jadwal->fresh()->is_closed);
    }

    public function test_close_manual_ignores_non_aktif_and_alumni(): void
    {
        $this->actingAs($this->petugas());
        $jadwal = $this->jadwalAktif();
        $aktif = $this->anggota('221111306', 'Anggota Tetap');
        $nonAktif = $this->anggota('221111307', 'Anggota Non Aktif');
        $alumni = $this->anggota('221111308', 'Anggota Alumni');

        $nonAktif->update(['status_anggota' => Anggota::STATUS_NON_AKTIF]);
        $alumni->update(['status_anggota' => Anggota::STATUS_ALUMNI]);

        $this->post(route('jadwal.tutup', $jadwal->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('absensi', ['anggota_id' => $aktif->id, 'status' => Absensi::STATUS_ALFA]);
        $this->assertDatabaseMissing('absensi', ['anggota_id' => $nonAktif->id]);
        $this->assertDatabaseMissing('absensi', ['anggota_id' => $alumni->id]);
        $this->assertDatabaseCount('absensi', 1);
    }

    public function test_close_manual_is_rejected_when_already_closed(): void
    {
        $this->actingAs($this->petugas());
        $jadwal = $this->jadwalAktif();
        $a = $this->anggota('221111304', 'Anggota Ganda');
        $jadwal->update(['is_closed' => true]);

        $this->post(route('jadwal.tutup', $jadwal->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('absensi', 0);
    }

    public function test_close_manual_is_rejected_on_hari_libur(): void
    {
        $this->actingAs($this->petugas());
        $jadwal = $this->jadwalAktif();
        $this->anggota('221111305', 'Anggota Libur');
        HariLibur::create(['tanggal' => Carbon::now()->toDateString(), 'keterangan' => 'Uji']);

        $this->post(route('jadwal.tutup', $jadwal->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('absensi', 0);
        $this->assertFalse($jadwal->fresh()->is_closed);
    }
}
