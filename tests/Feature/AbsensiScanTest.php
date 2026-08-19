<?php

namespace Tests\Feature;

use App\Livewire\AbsensiScan;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\User;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Concerns\SeedsFakultasProdi;
use Tests\TestCase;

class AbsensiScanTest extends TestCase
{
    use RefreshDatabase;
    use SeedsFakultasProdi;

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
        [$fakultas, $prodi] = $this->fakultasProdi('FMIPA', 'Matematika');

        return Anggota::create([
            'id_anggota' => 'TKD'.substr($nim, 0, 2).'-'.substr($nim, -3),
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

    private function petugasUntuk(Jadwal $jadwal): User
    {
        $user = User::factory()->create(['role' => User::ROLE_PETUGAS]);
        $jadwal->petugas()->attach($user->id);

        return $user;
    }

    public function test_manual_scan_records_hadir(): void
    {
        $jadwal = $this->jadwalAktif();
        $this->actingAs($this->petugasUntuk($jadwal));
        $anggota = $this->anggota('220011121', 'Anggota Scan');

        Livewire::test(AbsensiScan::class)
            ->set('nim', $anggota->nim)
            ->call('processManualInput');

        $this->assertDatabaseHas('absensi', [
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::now()->format('Y-m-d 00:00:00'),
            'status' => 'hadir',
            'sumber' => 'manual',
        ]);
    }

    public function test_duplicate_scan_is_rejected(): void
    {
        $jadwal = $this->jadwalAktif();
        $this->actingAs($this->petugasUntuk($jadwal));
        $anggota = $this->anggota('220011122', 'Anggota Duplikat');

        Livewire::test(AbsensiScan::class)
            ->set('nim', $anggota->nim)
            ->call('processManualInput')
            ->set('nim', $anggota->nim)
            ->call('processManualInput');

        $this->assertDatabaseCount('absensi', 1);
    }

    public function test_scan_by_qr_id_anggota_records_hadir(): void
    {
        $jadwal = $this->jadwalAktif();
        $this->actingAs($this->petugasUntuk($jadwal));
        $anggota = $this->anggota('220011123', 'Anggota QR');

        Livewire::test(AbsensiScan::class)
            ->call('processScanInput', $anggota->id_anggota);

        $this->assertDatabaseHas('absensi', [
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'hadir',
            'sumber' => 'scan',
        ]);
    }

    public function test_scan_rejected_on_hari_libur(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_PETUGAS]));
        $this->jadwalAktif();
        $anggota = $this->anggota('220011124', 'Anggota Libur');
        HariLibur::create(['tanggal' => Carbon::now()->toDateString(), 'keterangan' => 'Uji Libur']);

        Livewire::test(AbsensiScan::class)
            ->set('nim', $anggota->nim)
            ->call('processManualInput');

        $this->assertDatabaseCount('absensi', 0);
    }

    public function test_scan_rejected_when_no_active_jadwal(): void
    {
        $now = Carbon::now();
        $jadwal = Jadwal::create([
            'hari' => $this->hariIni(),
            'jam_start' => $now->copy()->addHours(2)->format('H:i:s'),
            'jam_close' => $now->copy()->addHours(3)->format('H:i:s'),
        ]);
        $this->actingAs($this->petugasUntuk($jadwal));
        $anggota = $this->anggota('220011125', 'Anggota NoJadwal');

        Livewire::test(AbsensiScan::class)
            ->set('nim', $anggota->nim)
            ->call('processManualInput');

        $this->assertDatabaseCount('absensi', 0);
    }

    public function test_reports_today_schedule_when_window_not_open(): void
    {
        $now = Carbon::now();
        $jadwal = Jadwal::create([
            'hari' => $this->hariIni(),
            'jam_start' => $now->copy()->addHours(2)->format('H:i:s'),
            'jam_close' => $now->copy()->addHours(3)->format('H:i:s'),
        ]);
        $this->actingAs($this->petugasUntuk($jadwal));

        $component = Livewire::test(AbsensiScan::class);
        $component->assertSet('jadwalId', null);
        $component->assertSee('Jadwal hari ini');
        $this->assertContains($component->instance()->jadwalHariIniStatus, ['belum dibuka', 'sudah ditutup']);
    }

    public function test_refresh_jadwal_detects_active_schedule_later(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_PETUGAS]));

        $component = Livewire::test(AbsensiScan::class);
        $component->assertSet('jadwalId', null);

        $jadwal = $this->jadwalAktif();

        $component->call('refreshJadwal');
        $component->assertSet('jadwalId', $jadwal->id);
        $component->assertNotSet('jadwalInfo', null);
    }
}
