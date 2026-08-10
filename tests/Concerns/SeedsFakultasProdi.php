<?php

namespace Tests\Concerns;

use App\Models\Fakultas;
use App\Models\ProgramStudi;

trait SeedsFakultasProdi
{
    /**
     * Buat (atau ambil bila sudah ada) fakultas dan prodi, kembalikan pasangan modelnya.
     *
     * @return array{0: Fakultas, 1: ProgramStudi}
     */
    protected function fakultasProdi(string $namaFakultas, string $namaProdi): array
    {
        $fakultas = Fakultas::firstOrCreate(['nama_fakultas' => $namaFakultas]);
        $prodi = ProgramStudi::firstOrCreate([
            'fakultas_id' => $fakultas->id,
            'nama_prodi' => $namaProdi,
        ]);

        return [$fakultas, $prodi];
    }
}
