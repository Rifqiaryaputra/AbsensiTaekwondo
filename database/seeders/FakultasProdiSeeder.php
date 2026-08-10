<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FakultasProdiSeeder extends Seeder
{
    protected array $fakultasProdi = [
        'Fakultas Agama Islam (FAI)' => ['Pendidikan Agama Islam', 'Bahasa dan Sastra Arab', 'Ilmu Hadis', 'Perbankan Syariah'],
        'Fakultas Ekonomi dan Bisnis (FEB)' => ['Manajemen', 'Akuntansi', 'Ekonomi Pembangunan', 'Bisnis Jasa Makanan'],
        'Farmasi' => ['Farmasi'],
        'Hukum (FH)' => ['Ilmu Hukum'],
        'Kedokteran (FK)' => ['Kedokteran'],
        'Fakultas Keguruan dan Ilmu Pendidikan (FKIP)' => ['Bimbingan Konseling (BK)', 'Pendidikan Guru Sekolah Dasar (PGSD)', 'Pendidikan Guru Pendidikan Anak Usia Dini (PGPAUD)', 'Pendidikan Matematika', 'Pendidikan Fisika', 'Pendidikan Biologi', 'Pendidikan Bahasa Inggris', 'Pendidikan Bahasa dan Sastra Indonesia', 'Pendidikan Pancasila dan Kewarganegaraan', 'Pendidikan Vokasional Teknologi Otomotif (PVTO)', 'Pendidikan Vokasional Teknik Elektronika (PVTE)'],
        'Fakultas Kesehatan Masyarakat (FKM)' => ['Kesehatan Masyarakat', 'Gizi'],
        'Psikologi' => ['Psikologi'],
        'Fakultas Sastra, Budaya, Komunikasi (FSBK)' => ['Ilmu Komunikasi', 'Sastra Inggris', 'Sastra Indonesia'],
        'Fakultas Teknologi Industri (FTI)' => ['Informatika', 'Teknik Industri', 'Teknik Kimia', 'Teknik Elektro', 'Teknologi Pangan'],
        'Fakultas Sains dan Teknologi Terapan (FAST)' => ['Biologi', 'Fisika', 'Matematika', 'Sistem Informasi'],
    ];

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ProgramStudi::truncate();
        Fakultas::truncate();
        Schema::enableForeignKeyConstraints();

        foreach ($this->fakultasProdi as $namaFakultas => $prodis) {
            $fakultas = Fakultas::create(['nama_fakultas' => $namaFakultas]);

            foreach ($prodis as $namaProdi) {
                $fakultas->programStudi()->create(['nama_prodi' => $namaProdi]);
            }
        }
    }
}
