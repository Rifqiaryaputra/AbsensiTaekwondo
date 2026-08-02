<?php

namespace Tests\Feature;

use App\Http\Controllers\DashboardController;
use App\Livewire\DaftarAnggota;
use App\Livewire\DashboardStats;
use App\Livewire\KelolaHariLibur;
use App\Livewire\KelolaJadwal;
use App\Livewire\KelolaPetugas;
use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\User;
use App\Services\AnggotaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class DataBindingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function anggotaUser(string $nim = '220011501', string $nama = 'Anggota DB'): array
    {
        $anggota = Anggota::create([
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
        $user = User::factory()->create(['role' => User::ROLE_ANGGOTA, 'anggota_id' => $anggota->id]);

        return [$anggota, $user];
    }

    public function test_daftar_anggota_create_appears_in_table(): void
    {
        $this->actingAs($this->admin());
        $nama = 'Anggota Baru Test';

        Livewire::test(DaftarAnggota::class)
            ->set('nama', $nama)
            ->set('nim', '2200115021')
            ->set('tglLahir', '2003-05-15')
            ->set('jk', 'L')
            ->set('wa', '6281234567890')
            ->set('fakultas', 'Matematika dan Ilmu Pengetahuan Alam (FMIPA)')
            ->set('prodi', 'Matematika')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee($nama);

        $this->assertDatabaseHas('anggota', ['nama_lengkap' => $nama, 'nim' => '2200115021']);

        $anggota = Anggota::where('nim', '2200115021')->first();
        app(AnggotaService::class)->deleteFile($anggota->qr_code);
    }

    public function test_hari_libur_create_appears_in_table(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(KelolaHariLibur::class)
            ->set('tanggal', '2026-10-10')
            ->set('keterangan', 'Hari Kesaktian')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Hari Kesaktian');

        $this->assertDatabaseHas('hari_libur', ['tanggal' => '2026-10-10 00:00:00', 'keterangan' => 'Hari Kesaktian']);
    }

    public function test_petugas_create_separate_account_and_delete(): void
    {
        $this->actingAs($this->admin());
        [$anggota, $user] = $this->anggotaUser('220011503', 'Calon Petugas');

        Livewire::test(KelolaPetugas::class)
            ->set('selectedUserId', $user->id)
            ->set('email', 'petugas@taekwondo.test')
            ->set('password', 'secret123')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('petugas@taekwondo.test');

        // Akun anggota asli TIDAK berubah rolenya
        $user->refresh();
        $this->assertSame(User::ROLE_ANGGOTA, $user->role);

        // Akun petugas BARU dibuat, terhubung ke anggota yang sama
        $petugas = User::where('email', 'petugas@taekwondo.test')->first();
        $this->assertNotNull($petugas);
        $this->assertSame(User::ROLE_PETUGAS, $petugas->role);
        $this->assertSame($anggota->id, $petugas->anggota_id);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('secret123', $petugas->password));

        // Hapus petugas -> HANYA akun petugas yang dihapus
        Livewire::test(KelolaPetugas::class)
            ->call('openDelete', $petugas->id, $petugas->name)
            ->call('confirmDelete');

        $this->assertDatabaseMissing('users', ['id' => $petugas->id]);
        $user->refresh();
        $this->assertSame(User::ROLE_ANGGOTA, $user->role);
        $this->assertDatabaseHas('anggota', ['id' => $anggota->id]);
    }

    public function test_petugas_cannot_be_created_twice_for_same_anggota(): void
    {
        $this->actingAs($this->admin());
        [$anggota, $user] = $this->anggotaUser('220011509', 'Anggota Dua Petugas');

        Livewire::test(KelolaPetugas::class)
            ->set('selectedUserId', $user->id)
            ->set('email', 'petugas1@taekwondo.test')
            ->set('password', 'secret123')
            ->call('save')
            ->assertHasNoErrors();

        Livewire::test(KelolaPetugas::class)
            ->set('selectedUserId', $user->id)
            ->set('email', 'petugas2@taekwondo.test')
            ->set('password', 'secret123')
            ->call('save');

        $this->assertDatabaseMissing('users', ['email' => 'petugas2@taekwondo.test']);
        $this->assertDatabaseHas('users', ['email' => 'petugas1@taekwondo.test', 'role' => User::ROLE_PETUGAS]);
    }

    public function test_jadwal_create_syncs_petugas_pivot(): void
    {
        $this->actingAs($this->admin());
        [$anggota, $petugas] = $this->anggotaUser('220011504', 'Petugas Jadwal');
        $petugas->update(['role' => User::ROLE_PETUGAS]);

        Livewire::test(KelolaJadwal::class)
            ->set('hari', 'Senin')
            ->set('jamMulai', '16:00')
            ->set('jamTutup', '18:00')
            ->set('petugasTerpilih', [(string) $petugas->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Senin');

        $jadwal = Jadwal::where('hari', 'Senin')->first();
        $this->assertNotNull($jadwal);
        $this->assertDatabaseHas('jadwal_petugas', ['jadwal_id' => $jadwal->id, 'user_id' => $petugas->id]);
    }

    public function test_jadwal_edit_hydrates_selected_data(): void
    {
        $this->actingAs($this->admin());
        [$anggota, $petugas] = $this->anggotaUser('220011507', 'Petugas Edit');
        $petugas->update(['role' => User::ROLE_PETUGAS]);
        $jadwal = Jadwal::create(['hari' => 'Sabtu', 'jam_start' => '08:00:00', 'jam_close' => '10:00:00']);
        $jadwal->petugas()->attach($petugas->id);

        Livewire::test(KelolaJadwal::class)
            ->call('edit', $jadwal->id)
            ->assertSet('showForm', true)
            ->assertSet('editingId', $jadwal->id)
            ->assertSet('hari', 'Sabtu')
            ->assertSet('jamMulai', '08:00')
            ->assertSet('jamTutup', '10:00')
            ->assertSet('petugasTerpilih', [(string) $petugas->id])
            ->assertSeeHtml('value="Sabtu" selected')
            ->assertSeeHtml('value="08:00"')
            ->assertSeeHtml('value="10:00"')
            ->assertSeeHtml('value="'.$petugas->id.'" checked');
    }

    public function test_dashboard_export_laporan_is_real_link(): void
    {
        $this->actingAs($this->admin());
        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Export Laporan');
        $response->assertSee('/rekap/export?', false);
        $response->assertDontSee('Export laporan tersedia pada fase backend');
    }

    public function test_daftar_anggota_edit_hydrates_form(): void
    {
        $this->actingAs($this->admin());
        $anggota = Anggota::create([
            'id_anggota' => 'TKD22-601',
            'nama_lengkap' => 'Edit Hydrate',
            'nim' => '2200116011',
            'tanggal_lahir' => '2002-07-19',
            'jenis_kelamin' => 'P',
            'no_whatsapp' => '6281377889900',
            'fakultas' => 'Sastra, Budaya, dan Komunikasi',
            'program_studi' => 'Ilmu Komunikasi',
            'no_bpjs' => '123456',
            'qr_code' => 'qr-codes/test.svg',
        ]);

        Livewire::test(DaftarAnggota::class)
            ->call('edit', $anggota->id)
            ->assertSet('showForm', true)
            ->assertSet('editingId', $anggota->id)
            ->assertSet('nama', 'Edit Hydrate')
            ->assertSet('nim', '2200116011')
            ->assertSet('tglLahir', '2002-07-19')
            ->assertSet('jk', 'P')
            ->assertSet('wa', '6281377889900')
            ->assertSet('fakultas', 'Sastra, Budaya, dan Komunikasi')
            ->assertSet('prodi', 'Ilmu Komunikasi')
            ->assertSet('bpjs', '123456');
    }

    public function test_daftar_anggota_import_from_csv(): void
    {
        $this->actingAs($this->admin());

        $csv = "NAMA LENGKAP,NIM,TANGGAL LAHIR,JENIS KELAMIN,NO WHATSAPP,FAKULTAS,PROGRAM STUDI,NO BPJS\n"
            ."Import Satu,2200116020,2003-01-01,L,6281234567890,Matematika dan Ilmu Pengetahuan Alam (FMIPA),Matematika,\n";

        Livewire::test(DaftarAnggota::class)
            ->set('importFile', UploadedFile::fake()->createWithContent('data.csv', $csv))
            ->call('import')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('anggota', ['nim' => '2200116020', 'nama_lengkap' => 'Import Satu']);
        $this->assertDatabaseHas('users', ['email' => '2200116020@webmail.uad.ac.id']);

        $anggota = Anggota::where('nim', '2200116020')->first();
        $anggota->user?->forceDelete();
        app(AnggotaService::class)->deleteFile($anggota->qr_code);
        $anggota->forceDelete();
    }

    public function test_daftar_anggota_rejects_invalid_nim_and_wa(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(DaftarAnggota::class)
            ->set('nama', 'Anggota Invalid')
            ->set('nim', '12345')
            ->set('tglLahir', '2003-05-15')
            ->set('jk', 'L')
            ->set('wa', '0812345678')
            ->set('fakultas', 'Matematika dan Ilmu Pengetahuan Alam (FMIPA)')
            ->set('prodi', 'Matematika')
            ->call('save')
            ->assertHasErrors(['nim', 'wa']);

        $this->assertDatabaseCount('anggota', 0);
    }

    public function test_daftar_anggota_formats_nama_to_title_case(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(DaftarAnggota::class)
            ->set('nama', 'anggota kecil')
            ->set('nim', '2200116030')
            ->set('tglLahir', '2003-05-15')
            ->set('jk', 'L')
            ->set('wa', '6281234567890')
            ->set('fakultas', 'Hukum')
            ->set('prodi', 'Hukum')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('anggota', ['nim' => '2200116030', 'nama_lengkap' => 'Anggota Kecil']);

        $anggota = Anggota::where('nim', '2200116030')->first();
        $anggota->user?->forceDelete();
        app(AnggotaService::class)->deleteFile($anggota->qr_code);
        $anggota->forceDelete();
    }

    public function test_incomplete_member_is_flagged(): void
    {
        $complete = Anggota::create([
            'id_anggota' => 'TKD22-701', 'nama_lengkap' => 'Lengkap', 'nim' => '2200177010',
            'tanggal_lahir' => '2003-01-01', 'jenis_kelamin' => 'L', 'no_whatsapp' => '6281234567890',
            'fakultas' => 'Matematika dan Ilmu Pengetahuan Alam (FMIPA)', 'program_studi' => 'Matematika',
            'qr_code' => 'qr-codes/test.svg',
        ]);
        $incomplete = Anggota::create([
            'id_anggota' => 'TKD22-702', 'nama_lengkap' => 'Tidak Lengkap', 'nim' => '2200177020',
            'tanggal_lahir' => '2003-01-01', 'jenis_kelamin' => 'L', 'no_whatsapp' => '0812345678',
            'fakultas' => 'Hukum', 'program_studi' => '-',
            'qr_code' => 'qr-codes/test.svg',
        ]);

        $component = new DaftarAnggota;
        $this->assertFalse($component->isIncomplete($complete));
        $this->assertTrue($component->isIncomplete($incomplete));
    }

    public function test_member_visible_after_refresh_and_edit_renders_values(): void
    {
        $this->actingAs($this->admin());
        $anggota = Anggota::create([
            'id_anggota' => 'TKD22-705',
            'nama_lengkap' => 'Refresh Member',
            'nim' => '2200177050',
            'tanggal_lahir' => '2002-03-11',
            'jenis_kelamin' => 'P',
            'no_whatsapp' => '6281234567890',
            'fakultas' => 'Hukum',
            'program_studi' => 'Hukum',
            'qr_code' => 'qr-codes/test.svg',
        ]);

        // Simulasi refresh: render ulang halaman list
        $page = $this->get(route('anggota.index'));
        $page->assertOk();
        $page->assertSee('Refresh Member');

        // Edit: nilai harus benar-benar muncul di HTML modal (bukan hanya property)
        Livewire::test(DaftarAnggota::class)
            ->call('edit', $anggota->id)
            ->assertSet('editingId', $anggota->id)
            ->assertSet('nama', 'Refresh Member')
            ->assertSet('nim', '2200177050')
            ->assertSet('fakultas', 'Hukum')
            ->assertSet('prodi', 'Hukum')
            ->assertSeeHtml('value="Refresh Member"')
            ->assertSeeHtml('value="2200177050"')
            ->assertSeeHtml('value="6281234567890"')
            ->assertSeeHtml('value="2002-03-11"')
            ->assertSeeHtml('value="Hukum" selected')
            ->assertSeeHtml('>Hukum</option>');
    }

    public function test_hari_libur_edit_hydrates_values(): void
    {
        $this->actingAs($this->admin());
        $libur = HariLibur::create(['tanggal' => '2026-11-15', 'keterangan' => 'Uji Edit']);

        Livewire::test(KelolaHariLibur::class)
            ->call('openForm', $libur->id)
            ->assertSet('tanggal', '2026-11-15')
            ->assertSet('keterangan', 'Uji Edit')
            ->assertSeeHtml('value="2026-11-15"')
            ->assertSeeHtml('value="Uji Edit"');
    }

    public function test_dependent_prodi_populates_after_fakultas(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(DaftarAnggota::class)
            ->set('fakultas', 'Hukum')
            ->assertSet('prodi', '')
            ->assertSeeHtml('<option value="Hukum">Hukum</option>');

        Livewire::test(DaftarAnggota::class)
            ->set('fakultas', 'Teknologi Industri')
            ->assertSeeHtml('value="Informatika"')
            ->assertSeeHtml('value="Teknik Elektro"');
    }

    public function test_pagination_prev_next_works(): void
    {
        $this->actingAs($this->admin());

        foreach (range(1, 12) as $i) {
            Anggota::create([
                'id_anggota' => 'TKD22-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'nama_lengkap' => 'Member '.$i,
                'nim' => '22002'.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'tanggal_lahir' => '2003-01-01',
                'jenis_kelamin' => 'L',
                'no_whatsapp' => '6281234567890',
                'fakultas' => 'Hukum',
                'program_studi' => 'Hukum',
                'qr_code' => 'qr-codes/test.svg',
            ]);
        }

        Livewire::test(DaftarAnggota::class)
            ->assertSet('paginators.page', 1)
            ->assertSee('>Member 1<', false)
            ->assertDontSee('>Member 6<', false)
            ->assertSeeHtml('wire:click="previousPage" disabled')
            ->call('gotoPage', 2)
            ->assertSet('paginators.page', 2)
            ->assertSee('>Member 6<', false)
            ->assertDontSee('>Member 1<', false)
            ->assertDontSee('wire:click="previousPage" disabled', false)
            ->call('gotoPage', 3)
            ->assertSet('paginators.page', 3)
            ->assertSee('>Member 11<', false)
            ->assertDontSee('>Member 6<', false)
            ->call('gotoPage', 1)
            ->assertSet('paginators.page', 1)
            ->assertSee('>Member 1<', false)
            ->assertSeeHtml('wire:click="previousPage" disabled');
    }

    public function test_prodi_filter_is_independent_of_fakultas(): void
    {
        $this->actingAs($this->admin());

        Anggota::create([
            'id_anggota' => 'TKD22-801', 'nama_lengkap' => 'Anggota Matematika', 'nim' => '2200280100',
            'tanggal_lahir' => '2003-01-01', 'jenis_kelamin' => 'L', 'no_whatsapp' => '6281234567890',
            'fakultas' => 'Matematika dan Ilmu Pengetahuan Alam (FMIPA)', 'program_studi' => 'Matematika',
            'qr_code' => 'qr-codes/test.svg',
        ]);
        Anggota::create([
            'id_anggota' => 'TKD22-802', 'nama_lengkap' => 'Anggota Hukum', 'nim' => '2200280200',
            'tanggal_lahir' => '2003-01-01', 'jenis_kelamin' => 'L', 'no_whatsapp' => '6281234567890',
            'fakultas' => 'Hukum', 'program_studi' => 'Hukum',
            'qr_code' => 'qr-codes/test.svg',
        ]);

        // Daftar prodi filter tanpa memilih fakultas = semua prodi (flatten)
        $component = new DaftarAnggota;
        $prodis = $component->prodiFilterList();
        $this->assertContains('Matematika', $prodis);
        $this->assertContains('Hukum', $prodis);

        // Filter prodi berjalan independen tanpa perlu memilih fakultas
        Livewire::test(DaftarAnggota::class)
            ->set('prodiFilter', 'Matematika')
            ->assertSee('>Anggota Matematika<', false)
            ->assertDontSee('>Anggota Hukum<', false);
    }

    public function test_dashboard_shows_real_statistics(): void
    {
        [$anggotaA, $userA] = $this->anggotaUser('220011505', 'Anggota A');
        [$anggotaB, $userB] = $this->anggotaUser('220011506', 'Anggota B');
        $petugas = $userA;
        $petugas->update(['role' => User::ROLE_PETUGAS]);
        foreach (['2026-01-01', '2026-05-01', '2026-08-17'] as $i => $tgl) {
            HariLibur::create(['tanggal' => $tgl, 'keterangan' => 'Libur '.$i]);
        }
        $jadwal = Jadwal::create(['hari' => 'Senin', 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);
        Absensi::create([
            'anggota_id' => $anggotaA->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::now()->toDateString(),
            'status' => 'hadir',
            'sumber' => 'scan',
        ]);

        $this->actingAs($this->admin());
        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('TOTAL ANGGOTA');

        Livewire::test(DashboardStats::class)
            ->assertSet('kpis.0.value', 2)
            ->assertSet('kpis.1.value', 1)
            ->assertSet('kpis.2.value', 3)
            ->assertSet('kpis.3.value', 1)
            ->assertSet('gender.total', 2)
            ->assertSet('gender.laki.jumlah', 2)
            ->assertSet('kehadiranHariIni.hadir', 1);
    }
}
