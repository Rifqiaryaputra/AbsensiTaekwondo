<?php

namespace App\Exports;

use App\Models\Anggota;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DatabaseAnggotaExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected string $search = '',
        protected string $fakultas = '',
        protected string $prodi = ''
    ) {
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        $search = strtolower(trim($this->search));

        return Anggota::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(nama_lengkap) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(nim) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(id_anggota) LIKE ?', ["%{$search}%"]);
                });
            })
            ->when($this->fakultas !== '', fn ($query) => $query->where('fakultas', $this->fakultas))
            ->when($this->prodi !== '', fn ($query) => $query->where('program_studi', $this->prodi))
            ->orderBy('id_anggota');
    }

    public function headings(): array
    {
        return [
            'ID ANGGOTA',
            'NAMA LENGKAP',
            'NIM',
            'TANGGAL LAHIR',
            'JENIS KELAMIN',
            'NO. WHATSAPP',
            'FAKULTAS',
            'PROGRAM STUDI',
            'NO. BPJS',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id_anggota,
            $row->nama_lengkap,
            $row->nim,
            $row->tanggal_lahir?->format('Y-m-d'),
            $row->jenis_kelamin,
            $row->no_whatsapp,
            $row->fakultas,
            $row->program_studi,
            $row->no_bpjs,
        ];
    }
}
