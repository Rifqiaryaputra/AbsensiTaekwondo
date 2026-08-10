<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\HariLibur;
use App\Models\IzinSakit;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDummyData extends Command
{
    protected $signature = 'app:reset-dummy';

    protected $description = 'Hapus data percobaan/dummy (anggota, absensi, izin, user non-admin) tapi amankan Admin, Settings, dan Master Data (Fakultas & Program Studi)';

    public function handle(): int
    {
        $hapusJadwal = $this->confirm('Hapus juga data Jadwal dan Hari Libur?', false);

        Schema::disableForeignKeyConstraints();

        Absensi::truncate();
        IzinSakit::truncate();
        Anggota::truncate();

        if ($hapusJadwal) {
            Jadwal::truncate();
            HariLibur::truncate();
        }

        // Pivot jadwal_petugas menunjuk ke user petugas/anggota & jadwal: kosongkan agar tidak menyisakan referensi menggantung
        DB::table('jadwal_petugas')->truncate();

        User::where('role', '!=', User::ROLE_ADMIN)->delete();

        if (DB::getDriverName() === 'mysql') {
            // MySQL otomatis menyesuaikan ke max(users.id)+1 admin, sehingga user berikutnya dapat ID benar
            DB::statement('ALTER TABLE users AUTO_INCREMENT = 1;');
        }

        Schema::enableForeignKeyConstraints();

        $this->info('Data percobaan berhasil dihapus dan Auto-Increment direset! (Admin & Master Data aman).');

        return self::SUCCESS;
    }
}
