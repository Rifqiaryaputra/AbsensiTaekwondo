<?php

namespace App\Imports;

use App\Models\Anggota;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Services\AnggotaService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

class AnggotaImport implements SkipsOnError, ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public int $skipped = 0;

    /**
     * Kolom yang diharapkan (heading Excel):
     * NAMA LENGKAP | NIM | TANGGAL LAHIR | JENIS KELAMIN | NO WHATSAPP | FAKULTAS | PROGRAM STUDI | NO BPJS
     */
    public function collection(Collection $rows): void
    {
        $service = app(AnggotaService::class);

        foreach ($rows as $row) {
            $nim = trim((string) ($row['nim'] ?? ''));
            $nama = ucwords(trim((string) ($row['nama_lengkap'] ?? $row['nama'] ?? '')));
            $jk = strtoupper(trim((string) ($row['jenis_kelamin'] ?? 'L')));

            if ($nim === '' || $nama === '' || ! in_array($jk, ['L', 'P'])) {
                $this->skipped++;

                continue;
            }

            if (Anggota::where('nim', $nim)->exists()) {
                $this->skipped++;

                continue;
            }

            try {
                $idAnggota = $service->generateIdAnggota($nim);
                $qrCode = $service->generateQrCode($idAnggota);

                $tanggalLahir = $this->parseTanggal($row['tanggal_lahir'] ?? null);
                $noBpjs = trim((string) ($row['no_bpjs'] ?? ''));

                $namaFakultas = trim((string) ($row['fakultas'] ?? '-')) ?: '-';
                $namaProdi = trim((string) ($row['program_studi'] ?? '-')) ?: '-';

                $anggota = Anggota::create([
                    'id_anggota' => $idAnggota,
                    'nama_lengkap' => $nama,
                    'nim' => $nim,
                    'tanggal_lahir' => $tanggalLahir,
                    'jenis_kelamin' => $jk,
                    'no_whatsapp' => trim((string) ($row['no_whatsapp'] ?? '')) ?: null,
                    'fakultas_id' => $this->resolveFakultasId($namaFakultas),
                    'program_studi_id' => $this->resolveProdiId($namaFakultas, $namaProdi),
                    'no_bpjs' => $noBpjs !== '' ? $noBpjs : null,
                    'qr_code' => $qrCode,
                ]);

                $service->createUser($anggota);
                $this->imported++;
            } catch (Throwable) {
                $this->skipped++;
            }
        }
    }

    public function onError(Throwable $e): void
    {
        // Lewati baris yang bermasalah.
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    private function resolveFakultasId(string $namaFakultas): ?int
    {
        return Fakultas::where('nama_fakultas', $namaFakultas)->value('id');
    }

    private function resolveProdiId(string $namaFakultas, string $namaProdi): ?int
    {
        $fakultasId = Fakultas::where('nama_fakultas', $namaFakultas)->value('id');

        if (! $fakultasId) {
            return null;
        }

        return ProgramStudi::where('fakultas_id', $fakultasId)
            ->where('nama_prodi', $namaProdi)
            ->value('id');
    }

    private function parseTanggal(mixed $value): string
    {
        if (is_numeric($value)) {
            // Serial number Excel
            return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        if (is_string($value) && strtotime($value)) {
            return Carbon::parse($value)->format('Y-m-d');
        }

        return '2000-01-01';
    }
}
