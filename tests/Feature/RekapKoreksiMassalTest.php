<?php

namespace Tests\Feature;

use App\Livewire\RekapKehadiran;
use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\Jadwal;
use App\Models\User;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SeedsFakultasProdi;
use Tests\TestCase;

class RekapKoreksiMassalTest extends TestCase
{
    use RefreshDatabase;
    use SeedsFakultasProdi;

    private function jadwalHariIni(): Jadwal
    {
        return Jadwal::create([
            'hari' => app(JadwalService::class)->hariNama(Carbon::now()),
            'jam_start' => '16:00:00',
            'jam_close' => '18:00:00',
        ]);
    }

    private function anggota(string $nim, string $nama): Anggota
    {
        [$fakultas, $prodi] = $this->fakultasProdi('FMIPA', 'Matematika');

        return Anggota::create([
            'id_anggota' => 'TKD-'.substr($nim, -3),
            'nama_lengkap' => $nama,
            'nim' => $nim,
            'tanggal_lahir' => '2003-01-01',
            'jenis_kelamin' => 'L',
            'no_whatsapp' => '08123',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'qr_code' => 'qr-codes/test.svg',
        ]);
    }

    public function test_buka_modal_memuat_anggota_dan_status_absensi(): void
    {
        $jadwal = $this->jadwalHariIni();
        $anggota = $this->anggota('220011301', 'Anggota Rekap');
        $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));

        Absensi::create([
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::now()->toDateString(),
            'status' => Absensi::STATUS_HADIR,
            'sumber' => Absensi::SUMBER_SCAN,
        ]);

        Livewire::test(RekapKehadiran::class)
            ->call('bukaModalKoreksi')
            ->assertSet('showModalKoreksi', true)
            ->assertSet('tanggalKoreksi', Carbon::now()->toDateString())
            ->assertSet("dataKoreksi.{$anggota->id}", 'Hadir')
            ->set('tanggalKoreksi', Carbon::now()->subDay()->toDateString())
            ->assertSet("dataKoreksi.{$anggota->id}", 'Alfa');
    }

    public function test_simpan_koreksi_melakukan_upsert(): void
    {
        $jadwal = $this->jadwalHariIni();
        $anggota = $this->anggota('220011302', 'Anggota Upsert Rekap');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        Livewire::test(RekapKehadiran::class)
            ->call('bukaModalKoreksi')
            ->set("dataKoreksi.{$anggota->id}", 'Izin')
            ->call('simpanKoreksi')
            ->assertSet('showModalKoreksi', false)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('absensi', [
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'status' => Absensi::STATUS_IZIN,
            'sumber' => Absensi::SUMBER_MANUAL,
            'petugas_id' => $admin->id,
        ]);

        Livewire::test(RekapKehadiran::class)
            ->call('bukaModalKoreksi')
            ->set("dataKoreksi.{$anggota->id}", 'Hadir')
            ->call('simpanKoreksi')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('absensi', 1);
        $this->assertDatabaseHas('absensi', [
            'anggota_id' => $anggota->id,
            'status' => Absensi::STATUS_HADIR,
        ]);
    }
}
