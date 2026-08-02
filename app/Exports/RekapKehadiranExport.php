<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekapKehadiranExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected ?string $start = null,
        protected ?string $end = null
    ) {
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return Absensi::query()
            ->with('anggota')
            ->when($this->start, fn ($q) => $q->whereDate('tanggal', '>=', $this->start))
            ->when($this->end, fn ($q) => $q->whereDate('tanggal', '<=', $this->end))
            ->orderBy('tanggal');
    }

    public function headings(): array
    {
        return [
            'NAMA ANGGOTA',
            'NIM',
            'TANGGAL LATIHAN',
            'STATUS',
            'SUMBER',
        ];
    }

    public function map($row): array
    {
        $statusLabels = [
            Absensi::STATUS_HADIR => 'Hadir',
            Absensi::STATUS_IZIN => 'Izin',
            Absensi::STATUS_SAKIT => 'Sakit',
            Absensi::STATUS_ALFA => 'Alfa',
        ];

        return [
            $row->anggota->nama_lengkap ?? '-',
            $row->anggota->nim ?? '-',
            $row->tanggal->format('Y-m-d'),
            $statusLabels[$row->status] ?? $row->status,
            $row->sumber,
        ];
    }
}
