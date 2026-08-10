<?php

namespace Tests\Feature;

use App\Exports\RekapKehadiranExport;
use App\Livewire\DaftarAnggota;
use App\Livewire\DashboardStats;
use App\Livewire\KelolaHariLibur;
use App\Livewire\KelolaJadwal;
use App\Livewire\KelolaPetugas;
use App\Livewire\RekapKehadiran;
use App\Livewire\SettingsProfil;
use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\Fakultas;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\PengaturanProfil;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Services\AnggotaService;
use App\Services\JadwalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\Concerns\SeedsFakultasProdi;
use Tests\TestCase;

class DataBindingTest extends TestCase
{
    use RefreshDatabase;
    use SeedsFakultasProdi;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function anggotaUser(string $nim = '220011501', string $nama = 'Anggota DB'): array
    {
        [$fakultas, $prodi] = $this->fakultasProdi('FMIPA', 'Matematika');
        $anggota = Anggota::create([
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
        $user = User::factory()->create(['role' => User::ROLE_ANGGOTA, 'anggota_id' => $anggota->id]);

        return [$anggota, $user];
    }

    public function test_daftar_anggota_create_appears_in_table(): void
    {
        $this->actingAs($this->admin());
        $nama = 'Anggota Baru Test';
        [$fakultas, $prodi] = $this->fakultasProdi('Matematika dan Ilmu Pengetahuan Alam (FMIPA)', 'Matematika');

        Livewire::test(DaftarAnggota::class)
            ->set('nama', $nama)
            ->set('nim', '2200115021')
            ->set('tglLahir', '2003-05-15')
            ->set('jk', 'L')
            ->set('wa', '6281234567890')
            ->set('fakultas_id', (string) $fakultas->id)
            ->set('program_studi_id', (string) $prodi->id)
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
        $this->assertTrue(Hash::check('secret123', $petugas->password));

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

    public function test_dashboard_export_laporan_wired(): void
    {
        $this->actingAs($this->admin());
        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Export Laporan');
        $response->assertSee('wire:click="exportLaporan"', false);
        $response->assertDontSee('Export laporan tersedia pada fase backend');
    }

    public function test_dashboard_export_laporan_download(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(DashboardStats::class)
            ->call('exportLaporan')
            ->assertFileDownloaded();
    }

    public function test_laporan_dashboard_pdf_view_content(): void
    {
        $this->actingAs($this->admin());
        PengaturanProfil::create([
            'nama_unit_kegiatan' => 'UKM Taekwondo UAD',
            'nama_universitas' => 'Universitas Ahmad Dahlan',
            'alamat_sekretariat' => 'Jl. Ringroad Selatan',
        ]);
        $settings = PengaturanProfil::first();

        $html = view('pdf.laporan-dashboard', [
            'settings' => $settings,
            'start' => Carbon::now()->startOfMonth(),
            'end' => Carbon::now()->endOfMonth(),
            'totalLatihan' => 8,
            'totalLibur' => 2,
            'topTerajin' => [],
            'topAlfa' => [],
            'bulanLabel' => 'Agustus 2026',
        ])->render();

        $this->assertStringContainsString('UNIT KEGIATAN MAHASISWA', $html);
        $this->assertStringContainsString('Laporan Statistik Absensi Bulanan', $html);
        $this->assertStringContainsString('Agustus 2026', $html);
        $this->assertStringContainsString('Total Hari Latihan', $html);
        $this->assertStringContainsString('8 sesi', $html);
        $this->assertStringContainsString('Total Hari Libur', $html);
        $this->assertStringContainsString('Anggota Terajin (Top 3)', $html);
        $this->assertStringContainsString('Paling Sering Alfa (Top 5)', $html);
    }

    public function test_daftar_anggota_edit_hydrates_form(): void
    {
        $this->actingAs($this->admin());
        [$fakultas, $prodi] = $this->fakultasProdi('Sastra, Budaya, dan Komunikasi', 'Ilmu Komunikasi');
        $anggota = Anggota::create([
            'id_anggota' => 'TKD22-601',
            'nama_lengkap' => 'Edit Hydrate',
            'nim' => '2200116011',
            'tanggal_lahir' => '2002-07-19',
            'jenis_kelamin' => 'P',
            'no_whatsapp' => '6281377889900',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
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
            ->assertSet('fakultas_id', (string) $fakultas->id)
            ->assertSet('program_studi_id', (string) $prodi->id)
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
        [$fakultas, $prodi] = $this->fakultasProdi('Matematika dan Ilmu Pengetahuan Alam (FMIPA)', 'Matematika');

        Livewire::test(DaftarAnggota::class)
            ->set('nama', 'Anggota Invalid')
            ->set('nim', '12345')
            ->set('tglLahir', '2003-05-15')
            ->set('jk', 'L')
            ->set('wa', '0812345678')
            ->set('fakultas_id', (string) $fakultas->id)
            ->set('program_studi_id', (string) $prodi->id)
            ->call('save')
            ->assertHasErrors(['nim', 'wa']);

        $this->assertDatabaseCount('anggota', 0);
    }

    public function test_daftar_anggota_formats_nama_to_title_case(): void
    {
        $this->actingAs($this->admin());
        [$fakultas, $prodi] = $this->fakultasProdi('Hukum', 'Hukum');

        Livewire::test(DaftarAnggota::class)
            ->set('nama', 'anggota kecil')
            ->set('nim', '2200116030')
            ->set('tglLahir', '2003-05-15')
            ->set('jk', 'L')
            ->set('wa', '6281234567890')
            ->set('fakultas_id', (string) $fakultas->id)
            ->set('program_studi_id', (string) $prodi->id)
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
        [$fakultas, $prodi] = $this->fakultasProdi('Matematika dan Ilmu Pengetahuan Alam (FMIPA)', 'Matematika');
        [$fakultasIncomplete] = $this->fakultasProdi('Hukum', 'Hukum');

        $complete = Anggota::create([
            'id_anggota' => 'TKD22-701', 'nama_lengkap' => 'Lengkap', 'nim' => '2200177010',
            'tanggal_lahir' => '2003-01-01', 'jenis_kelamin' => 'L', 'no_whatsapp' => '6281234567890',
            'fakultas_id' => $fakultas->id, 'program_studi_id' => $prodi->id,
            'qr_code' => 'qr-codes/test.svg',
        ]);
        $incomplete = Anggota::create([
            'id_anggota' => 'TKD22-702', 'nama_lengkap' => 'Tidak Lengkap', 'nim' => '2200177020',
            'tanggal_lahir' => '2003-01-01', 'jenis_kelamin' => 'L', 'no_whatsapp' => '0812345678',
            'fakultas_id' => $fakultasIncomplete->id, 'program_studi_id' => null,
            'qr_code' => 'qr-codes/test.svg',
        ]);

        $component = new DaftarAnggota;
        $this->assertFalse($component->isIncomplete($complete));
        $this->assertTrue($component->isIncomplete($incomplete));
    }

    public function test_member_visible_after_refresh_and_edit_renders_values(): void
    {
        $this->actingAs($this->admin());
        [$fakultas, $prodi] = $this->fakultasProdi('Hukum', 'Hukum');
        $anggota = Anggota::create([
            'id_anggota' => 'TKD22-705',
            'nama_lengkap' => 'Refresh Member',
            'nim' => '2200177050',
            'tanggal_lahir' => '2002-03-11',
            'jenis_kelamin' => 'P',
            'no_whatsapp' => '6281234567890',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
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
            ->assertSet('fakultas_id', (string) $fakultas->id)
            ->assertSet('program_studi_id', (string) $prodi->id)
            ->assertSeeHtml('value="Refresh Member"')
            ->assertSeeHtml('value="2200177050"')
            ->assertSeeHtml('value="6281234567890"')
            ->assertSeeHtml('value="2002-03-11"')
            ->assertSeeHtml('value="'.$fakultas->id.'" selected')
            ->assertSeeHtml('value="'.$prodi->id.'" selected')
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
        [$fakultas] = $this->fakultasProdi('Teknologi Industri', 'Informatika');
        $pdElektro = ProgramStudi::create(['fakultas_id' => $fakultas->id, 'nama_prodi' => 'Teknik Elektro']);
        [$fakultasHukum, $pdHukum] = $this->fakultasProdi('Hukum', 'Hukum');

        Livewire::test(DaftarAnggota::class)
            ->set('fakultasFilter', (string) $fakultas->id)
            ->set('fakultas_id', (string) $fakultas->id)
            ->assertSet('program_studi_id', '')
            ->assertSeeHtml('value="'.$pdElektro->id.'" >Teknik Elektro</option>')
            ->assertDontSeeHtml('value="'.$pdHukum->id.'" >Hukum</option>');

        // Ganti fakultas -> prodi ikut di-reset & opsi menyesuaikan
        Livewire::test(DaftarAnggota::class)
            ->set('fakultasFilter', (string) $fakultasHukum->id)
            ->set('program_studi_id', (string) $pdElektro->id)
            ->set('fakultas_id', (string) $fakultasHukum->id)
            ->assertSet('program_studi_id', '')
            ->assertSeeHtml('value="'.$pdHukum->id.'" >Hukum</option>')
            ->assertDontSeeHtml('value="'.$pdElektro->id.'" >Teknik Elektro</option>');
    }

    public function test_pagination_prev_next_works(): void
    {
        $this->actingAs($this->admin());
        [$fakultas, $prodi] = $this->fakultasProdi('Hukum', 'Hukum');

        foreach (range(1, 12) as $i) {
            Anggota::create([
                'id_anggota' => 'TKD22-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'nama_lengkap' => 'Member '.$i,
                'nim' => '22002'.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'tanggal_lahir' => '2003-01-01',
                'jenis_kelamin' => 'L',
                'no_whatsapp' => '6281234567890',
                'fakultas_id' => $fakultas->id,
                'program_studi_id' => $prodi->id,
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

    public function test_prodi_filter_depends_on_fakultas(): void
    {
        $this->actingAs($this->admin());
        [$fakultasMipa, $prodiMat] = $this->fakultasProdi('Matematika dan Ilmu Pengetahuan Alam (FMIPA)', 'Matematika');
        [$fakultasHukum, $prodiHukum] = $this->fakultasProdi('Hukum', 'Hukum');

        Anggota::create([
            'id_anggota' => 'TKD22-801', 'nama_lengkap' => 'Anggota Matematika', 'nim' => '2200280100',
            'tanggal_lahir' => '2003-01-01', 'jenis_kelamin' => 'L', 'no_whatsapp' => '6281234567890',
            'fakultas_id' => $fakultasMipa->id, 'program_studi_id' => $prodiMat->id,
            'qr_code' => 'qr-codes/test.svg',
        ]);
        Anggota::create([
            'id_anggota' => 'TKD22-802', 'nama_lengkap' => 'Anggota Hukum', 'nim' => '2200280200',
            'tanggal_lahir' => '2003-01-01', 'jenis_kelamin' => 'L', 'no_whatsapp' => '6281234567890',
            'fakultas_id' => $fakultasHukum->id, 'program_studi_id' => $prodiHukum->id,
            'qr_code' => 'qr-codes/test.svg',
        ]);

        // Pilih fakultas -> daftar prodi filter menyempit + filter prodi di-reset
        Livewire::test(DaftarAnggota::class)
            ->set('fakultasFilter', (string) $fakultasMipa->id)
            ->assertSet('prodiFilter', '')
            ->assertSeeHtml('value="'.$prodiMat->id.'" >Matematika</option>');

        // Filter prodi berjalan dalam lingkup fakultas yang dipilih
        Livewire::test(DaftarAnggota::class)
            ->set('fakultasFilter', (string) $fakultasMipa->id)
            ->set('prodiFilter', (string) $prodiMat->id)
            ->assertSee('>Anggota Matematika<', false)
            ->assertDontSee('>Anggota Hukum<', false);
    }

    public function test_rekap_aggregates_totals_per_member_and_search(): void
    {
        $this->actingAs($this->admin());
        [$a1, $u1] = $this->anggotaUser('220011601', 'Rekap Satu');
        [$a2, $u2] = $this->anggotaUser('220011602', 'Rekap Dua');
        $jadwal = Jadwal::create(['hari' => 'Senin', 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);

        $base = Carbon::now()->startOfMonth();
        $d1 = $base->copy()->addDays(1)->toDateString();
        $d2 = $base->copy()->addDays(2)->toDateString();
        $d3 = $base->copy()->addDays(3)->toDateString();

        foreach ([
            ['hadir', $a1->id, $d1],
            ['hadir', $a1->id, $d2],
            ['sakit', $a1->id, $d3],
            ['alfa', $a2->id, $d1],
            ['izin', $a2->id, $d2],
        ] as [$status, $anggotaId, $date]) {
            Absensi::create([
                'anggota_id' => $anggotaId,
                'jadwal_id' => $jadwal->id,
                'tanggal' => $date,
                'status' => $status,
                'sumber' => 'scan',
            ]);
        }

        $comp = new RekapKehadiran;
        $comp->start = Carbon::now()->startOfMonth()->toDateString();
        $comp->end = Carbon::now()->endOfMonth()->toDateString();
        $rows = $comp->anggotaQuery()->get();
        $row1 = $rows->firstWhere('id', $a1->id);
        $row2 = $rows->firstWhere('id', $a2->id);

        $this->assertSame(2, $row1->total_hadir);
        $this->assertSame(1, $row1->total_sakit);
        $this->assertSame(0, $row1->total_alfa);
        $this->assertSame(1, $row2->total_alfa);
        $this->assertSame(1, $row2->total_izin);

        Livewire::test(RekapKehadiran::class)
            ->set('search', 'Rekap Dua')
            ->assertSee('Rekap Dua')
            ->assertDontSee('Rekap Satu');
    }

    public function test_rekap_excel_export_format_and_filter(): void
    {
        $this->actingAs($this->admin());
        [$a1, $u1] = $this->anggotaUser('220011603', 'Export Satu');
        [$a2, $u2] = $this->anggotaUser('220011604', 'Export Dua');
        $jadwal = Jadwal::create(['hari' => 'Senin', 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);

        $base = Carbon::now()->startOfMonth();
        foreach ([
            ['hadir', $a1->id, 1],
            ['sakit', $a1->id, 2],
            ['alfa', $a2->id, 1],
        ] as [$status, $anggotaId, $day]) {
            Absensi::create([
                'anggota_id' => $anggotaId,
                'jadwal_id' => $jadwal->id,
                'tanggal' => $base->copy()->addDays($day)->toDateString(),
                'status' => $status,
                'sumber' => 'scan',
            ]);
        }

        $start = Carbon::now()->startOfMonth()->toDateString();
        $end = Carbon::now()->endOfMonth()->toDateString();

        // Tanpa filter search -> semua anggota
        $path = 'rekap_test.xlsx';
        Excel::store(new RekapKehadiranExport('', $start, $end), $path, 'local', \Maatwebsite\Excel\Excel::XLSX);
        $rows = IOFactory::load(storage_path('app/private/'.$path))->getActiveSheet()->toArray(null, true, false);
        $this->assertCount(3, $rows);
        $this->assertSame(['No', 'NAMA ANGGOTA', 'NIM', 'TOTAL SAKIT', 'TOTAL IZIN', 'TOTAL ALFA', 'TOTAL HADIR'], $rows[0]);
        $this->assertSame([1, 'Export Satu', 220011603, 1, 0, 0, 1], $rows[1]);
        $this->assertSame([2, 'Export Dua', 220011604, 0, 0, 1, 0], $rows[2]);
        @unlink(storage_path('app/private/'.$path));

        // Filter search -> hanya satu anggota yang diexport
        $path2 = 'rekap_test2.xlsx';
        Excel::store(new RekapKehadiranExport('Export Dua', $start, $end), $path2, 'local', \Maatwebsite\Excel\Excel::XLSX);
        $rows2 = IOFactory::load(storage_path('app/private/'.$path2))->getActiveSheet()->toArray(null, true, false);
        $this->assertCount(2, $rows2);
        $this->assertSame([1, 'Export Dua', 220011604, 0, 0, 1, 0], $rows2[1]);
        @unlink(storage_path('app/private/'.$path2));
    }

    public function test_rekap_export_excel_and_pdf_download(): void
    {
        $this->actingAs($this->admin());
        [$a1, $u1] = $this->anggotaUser('220011605', 'Pdf Satu');
        $jadwal = Jadwal::create(['hari' => 'Senin', 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);
        Absensi::create([
            'anggota_id' => $a1->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => Carbon::now()->startOfMonth()->addDay()->toDateString(),
            'status' => 'hadir',
            'sumber' => 'scan',
        ]);

        Livewire::test(RekapKehadiran::class)
            ->call('exportExcel')
            ->assertFileDownloaded();

        Livewire::test(RekapKehadiran::class)
            ->call('exportPdf')
            ->assertFileDownloaded();
    }

    public function test_settings_profil_save_and_logo_upload(): void
    {
        $this->actingAs($this->admin());
        $logo = UploadedFile::fake()->image('logo.png', 100, 100);

        Livewire::test(SettingsProfil::class)
            ->set('namaUnit', 'UKM Taekwondo UAD')
            ->set('namaUniversitas', 'Universitas Ahmad Dahlan')
            ->set('alamat', 'Jl. Ringroad Selatan')
            ->set('logoKiri', $logo)
            ->call('save')
            ->assertHasNoErrors();

        $profil = PengaturanProfil::first();
        $this->assertSame('UKM Taekwondo UAD', $profil->nama_unit_kegiatan);
        $this->assertStringStartsWith('logos/unit-', $profil->logo_unit_kegiatan);
        $this->assertFileExists(public_path($profil->logo_unit_kegiatan));

        // Muat ulang komponen (simulasi refresh) -> data tetap ada
        Livewire::test(SettingsProfil::class)
            ->assertSet('namaUnit', 'UKM Taekwondo UAD')
            ->assertSet('namaUniversitas', 'Universitas Ahmad Dahlan')
            ->assertSet('alamat', 'Jl. Ringroad Selatan')
            ->assertSet('existingLogoKiri', $profil->logo_unit_kegiatan);

        if ($profil->logo_unit_kegiatan) {
            File::delete(public_path($profil->logo_unit_kegiatan));
        }
    }

    public function test_rekap_pdf_contains_letterhead(): void
    {
        $this->actingAs($this->admin());
        PengaturanProfil::create([
            'nama_unit_kegiatan' => 'UKM Taekwondo UAD',
            'nama_universitas' => 'Universitas Ahmad Dahlan',
            'alamat_sekretariat' => 'Jl. Ringroad Selatan',
        ]);
        $settings = PengaturanProfil::first();

        $html = view('pdf.rekap-kehadiran', [
            'anggota' => collect(),
            'summary' => ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0],
            'start' => '2026-08-01',
            'end' => '2026-08-31',
            'search' => '',
            'settings' => $settings,
        ])->render();

        $this->assertStringContainsString('UNIT KEGIATAN MAHASISWA', $html);
        $this->assertStringContainsString('UKM TAEKWONDO UAD', $html);
        $this->assertStringContainsString('UNIVERSITAS AHMAD DAHLAN', $html);
        $this->assertStringContainsString('Jl. Ringroad Selatan', $html);
        $this->assertStringContainsString('kop-line', $html);
    }

    public function test_total_latihan_excludes_holidays(): void
    {
        $this->actingAs($this->admin());
        $svc = app(JadwalService::class);
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $jadwalHari = $svc->hariNama($start);
        Jadwal::create(['hari' => $jadwalHari, 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);

        $totalOcc = 0;
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            if ($svc->hariNama($cursor) === $jadwalHari) {
                $totalOcc++;
            }
            $cursor->addDay();
        }

        $comp = new DashboardStats;
        $this->assertSame($totalOcc, $comp->totalLatihanBulanIni($start, $end));

        // +7 hari = hari yang sama; jadikan hari libur -> sesi itu tidak dihitung
        HariLibur::create(['tanggal' => $start->copy()->addDays(7)->toDateString(), 'keterangan' => 'Uji Libur']);
        $this->assertSame($totalOcc - 1, $comp->totalLatihanBulanIni($start, $end));
    }

    public function test_dashboard_chart_data_has_four_series(): void
    {
        $this->actingAs($this->admin());
        [$a1, $u1] = $this->anggotaUser('220011610', 'Chart Satu');
        $jadwal = Jadwal::create(['hari' => 'Senin', 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);
        $today = Carbon::now();
        Absensi::create([
            'anggota_id' => $a1->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => $today->toDateString(),
            'status' => 'hadir',
            'sumber' => 'scan',
        ]);

        $comp = new DashboardStats;
        $comp->bulan = now()->format('Y-m');
        $data = $comp->chartData();

        $this->assertCount(4, $data['datasets']);
        $this->assertSame(['Hadir', 'Izin', 'Sakit', 'Alfa'], array_column($data['datasets'], 'label'));
        $this->assertSame('#22c55e', $data['datasets'][0]['backgroundColor']);
        $this->assertSame('#3b82f6', $data['datasets'][1]['backgroundColor']);
        $this->assertSame('#eab308', $data['datasets'][2]['backgroundColor']);
        $this->assertSame('#ef4444', $data['datasets'][3]['backgroundColor']);

        // Label sumbu-X terformat "Sen, 3 Agu"
        Carbon::setLocale('id');
        $shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $label = $today->translatedFormat('D, j').' '.$shortMonths[$today->month - 1];
        $idx = array_search($label, $data['labels']);
        $this->assertNotFalse($idx);
        $this->assertGreaterThanOrEqual(1, $data['datasets'][0]['data'][$idx]);
    }

    public function test_bulan_options_dynamic_from_earliest_data(): void
    {
        $this->actingAs($this->admin());

        // Tanpa data -> hanya bulan berjalan
        $comp = new DashboardStats;
        $this->assertSame([now()->format('Y-m')], array_keys($comp->bulanOptions()));

        // Tambah absensi 2 bulan lalu -> opsi mulai dari bulan tsb
        $bulan2 = now()->copy()->subMonths(2)->startOfMonth();
        [$a, $u] = $this->anggotaUser('220011620', 'Bulan Test');
        $jadwal = Jadwal::create(['hari' => 'Senin', 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);
        Absensi::create([
            'anggota_id' => $a->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => $bulan2->toDateString(),
            'status' => 'hadir',
            'sumber' => 'scan',
        ]);

        $keys = array_keys($comp->bulanOptions());
        $this->assertSame($bulan2->format('Y-m'), $keys[0]);
        $this->assertSame(now()->format('Y-m'), $keys[array_key_last($keys)]);
    }

    public function test_dashboard_top3_aktif_and_alfa_with_foto(): void
    {
        $this->actingAs($this->admin());
        $jadwal = Jadwal::create(['hari' => 'Senin', 'jam_start' => '16:00:00', 'jam_close' => '18:00:00']);
        $base = Carbon::now()->startOfMonth();

        for ($i = 1; $i <= 4; $i++) {
            [$a, $u] = $this->anggotaUser('2200116'.str_pad((string) $i, 2, '0', STR_PAD_LEFT), 'Top '.$i);
            if ($i === 1) {
                $a->update(['foto_dobok' => 'dobok-photos/test-1.jpg']);
                $days = [1, 2, 3];
            } else {
                $days = [$i, $i + 10];
            }
            foreach ($days as $d) {
                Absensi::create([
                    'anggota_id' => $a->id,
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $base->copy()->addDays($d)->toDateString(),
                    'status' => 'hadir',
                    'sumber' => 'scan',
                ]);
            }
        }

        $comp = new DashboardStats;
        $comp->refresh();

        $this->assertCount(3, $comp->anggotaAktif);
        $this->assertLessThanOrEqual(3, count($comp->seringAlfa));
        $this->assertSame('dobok-photos/test-1.jpg', $comp->anggotaAktif[0]['foto']);
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
