<?php

namespace Tests\Feature;

use App\Livewire\PengajuanIzin;
use App\Models\Anggota;
use App\Models\IzinSakit;
use App\Models\Jadwal;
use App\Models\User;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PerizinanFlowTest extends TestCase
{
    use RefreshDatabase;

    private function hariNama(Carbon $date): string
    {
        return app(JadwalService::class)->hariNama($date);
    }

    private function anggota(): Anggota
    {
        return Anggota::create([
            'id_anggota' => 'TKD22-001',
            'nama_lengkap' => 'Anggota Izin',
            'nim' => '220011301',
            'tanggal_lahir' => '2003-01-01',
            'jenis_kelamin' => 'L',
            'no_whatsapp' => '08123',
            'fakultas' => 'FMIPA',
            'program_studi' => 'Matematika',
            'qr_code' => 'qr-codes/test.svg',
        ]);
    }

    private function actingAsAnggota(Anggota $anggota): User
    {
        $user = User::factory()->create(['role' => User::ROLE_ANGGOTA, 'anggota_id' => $anggota->id]);
        $this->actingAs($user);

        return $user;
    }

    public function test_approving_izin_creates_absensi_record(): void
    {
        $anggota = $this->anggota();
        $jadwal = Jadwal::create(['hari' => $this->hariNama(Carbon::now()), 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);

        $izin = IzinSakit::create([
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::tomorrow()->toDateString(),
            'jenis' => 'sakit',
            'keterangan' => 'uji',
            'status' => 'menunggu',
            'diajukan_pada' => now(),
        ]);

        $izin->update(['status' => 'disetujui', 'diproses_oleh' => null, 'diproses_pada' => now()]);

        $this->assertDatabaseHas('absensi', [
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => $izin->tanggal->format('Y-m-d H:i:s'),
            'status' => 'sakit',
            'sumber' => 'izin_disetujui',
            'izin_sakit_id' => $izin->id,
        ]);
    }

    public function test_cancelled_izin_does_not_create_absensi(): void
    {
        $anggota = $this->anggota();
        $jadwal = Jadwal::create(['hari' => $this->hariNama(Carbon::now()), 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);

        $izin = IzinSakit::create([
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::tomorrow()->toDateString(),
            'jenis' => 'izin',
            'keterangan' => 'uji',
            'status' => 'menunggu',
            'diajukan_pada' => now(),
        ]);

        $izin->update(['status' => 'dibatalkan']);

        $this->assertDatabaseMissing('absensi', ['izin_sakit_id' => $izin->id]);
    }

    public function test_pengajuan_rejected_when_less_than_2_hours_before_start(): void
    {
        $anggota = $this->anggota();
        $this->actingAsAnggota($anggota);

        $now = Carbon::now();
        Jadwal::create([
            'hari' => $this->hariNama($now),
            'jam_start' => $now->copy()->subHour()->format('H:i:s'),
            'jam_close' => $now->copy()->addHour()->format('H:i:s'),
        ]);

        Livewire::test(PengajuanIzin::class)
            ->set('tanggal', $now->toDateString())
            ->set('jenis', 'izin')
            ->set('keterangan', 'Mendadak')
            ->call('save')
            ->assertHasErrors('tanggal')
            ->assertDispatched('toast', title: 'Ditolak');

        $this->assertDatabaseCount('izin_sakit', 0);
    }

    public function test_pengajuan_allowed_for_future_date(): void
    {
        $anggota = $this->anggota();
        $this->actingAsAnggota($anggota);

        $future = Carbon::now()->addWeek();
        $jadwal = Jadwal::create([
            'hari' => $this->hariNama($future),
            'jam_start' => '16:00:00',
            'jam_close' => '18:00:00',
        ]);

        Livewire::test(PengajuanIzin::class)
            ->set('tanggal', $future->toDateString())
            ->set('jenis', 'izin')
            ->set('keterangan', 'Ada acara keluarga')
            ->call('save');

        $this->assertDatabaseHas('izin_sakit', [
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => $future->format('Y-m-d 00:00:00'),
            'jenis' => 'izin',
            'status' => 'menunggu',
        ]);
    }

    public function test_pengajuan_allowed_for_today_within_window(): void
    {
        $anggota = $this->anggota();
        $this->actingAsAnggota($anggota);

        $now = Carbon::now();
        $jadwal = Jadwal::create([
            'hari' => $this->hariNama($now),
            'jam_start' => $now->copy()->addHours(3)->format('H:i:s'),
            'jam_close' => $now->copy()->addHours(5)->format('H:i:s'),
        ]);

        Livewire::test(PengajuanIzin::class)
            ->set('tanggal', $now->toDateString())
            ->set('jenis', 'sakit')
            ->set('keterangan', 'Demam')
            ->call('save');

        $this->assertDatabaseHas('izin_sakit', [
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => $now->format('Y-m-d 00:00:00'),
            'jenis' => 'sakit',
            'status' => 'menunggu',
        ]);
    }

    public function test_toggle_form_opens_modal(): void
    {
        $anggota = $this->anggota();
        $this->actingAsAnggota($anggota);

        Livewire::test(PengajuanIzin::class)
            ->call('toggleForm', true)
            ->assertSet('showForm', true)
            ->assertSee('Formulir Pengajuan')
            ->call('toggleForm', false)
            ->assertSet('showForm', false);
    }

    private function createIzin(Anggota $anggota, Jadwal $jadwal, string $tanggal, string $jenis = 'izin'): IzinSakit
    {
        return IzinSakit::create([
            'anggota_id' => $anggota->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'keterangan' => 'uji',
            'status' => 'menunggu',
            'diajukan_pada' => now(),
        ]);
    }

    public function test_pengajuan_list_paginates_full_history(): void
    {
        $anggota = $this->anggota();
        $this->actingAsAnggota($anggota);

        $jadwal = Jadwal::create(['hari' => $this->hariNama(Carbon::now()), 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);

        $today = Carbon::now()->toDateString();
        $old = Carbon::now()->subDays(3)->toDateString();

        for ($i = 0; $i < 12; $i++) {
            $this->createIzin($anggota, $jadwal, $today, 'izin');
        }
        $this->createIzin($anggota, $jadwal, $old, 'sakit');

        $list = (new PengajuanIzin)->pengajuanQuery()->paginate(10);

        $this->assertCount(10, $list->items());
        $this->assertSame(13, $list->total());
    }

    public function test_pengajuan_allowed_at_monthly_quota_five(): void
    {
        $anggota = $this->anggota();
        $this->actingAsAnggota($anggota);

        $now = Carbon::now();
        $jadwal = Jadwal::create([
            'hari' => $this->hariNama($now),
            'jam_start' => $now->copy()->addHours(3)->format('H:i:s'),
            'jam_close' => $now->copy()->addHours(5)->format('H:i:s'),
        ]);

        foreach (range(1, 4) as $i) {
            $this->createIzin($anggota, $jadwal, $now->toDateString(), $i % 2 === 0 ? 'izin' : 'sakit');
        }

        Livewire::test(PengajuanIzin::class)
            ->set('tanggal', $now->toDateString())
            ->set('jenis', 'izin')
            ->set('keterangan', 'Kelima')
            ->call('save')
            ->assertHasNoErrors('tanggal');

        $this->assertDatabaseCount('izin_sakit', 5);
    }

    public function test_pengajuan_rejected_when_monthly_quota_exceeded(): void
    {
        $anggota = $this->anggota();
        $this->actingAsAnggota($anggota);

        $now = Carbon::now();
        $jadwal = Jadwal::create([
            'hari' => $this->hariNama($now),
            'jam_start' => $now->copy()->addHours(3)->format('H:i:s'),
            'jam_close' => $now->copy()->addHours(5)->format('H:i:s'),
        ]);

        foreach (range(1, 5) as $i) {
            $this->createIzin($anggota, $jadwal, $now->toDateString(), $i % 2 === 0 ? 'izin' : 'sakit');
        }

        Livewire::test(PengajuanIzin::class)
            ->set('tanggal', $now->toDateString())
            ->set('jenis', 'sakit')
            ->set('keterangan', 'Keenam')
            ->call('save')
            ->assertHasErrors('tanggal')
            ->assertDispatched('toast', title: 'Ditolak');

        $this->assertDatabaseCount('izin_sakit', 5);
    }

    public function test_pengajuan_rejected_for_past_date(): void
    {
        $anggota = $this->anggota();
        $this->actingAsAnggota($anggota);

        Livewire::test(PengajuanIzin::class)
            ->set('tanggal', Carbon::yesterday()->toDateString())
            ->set('jenis', 'izin')
            ->set('keterangan', 'Kemarin sakit')
            ->call('save');

        $this->assertDatabaseCount('izin_sakit', 0);
    }
}
