<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Anggota;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekapKehadiranExport implements FromQuery, WithHeadings, WithMapping
{
    protected int $index = 0;

    public function __construct(
        protected string $search = '',
        protected ?string $start = null,
        protected ?string $end = null
    ) {
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        $searchTerms = collect(explode(',', $this->search))
            ->map(fn ($term) => strtolower(trim($term)))
            ->filter()
            ->values()
            ->all();

        return Anggota::query()
            ->withCount([
                'absensi as total_hadir' => fn ($q) => $q->where('status', Absensi::STATUS_HADIR)->when($this->start, fn ($q) => $q->whereDate('tanggal', '>=', $this->start))->when($this->end, fn ($q) => $q->whereDate('tanggal', '<=', $this->end)),
                'absensi as total_sakit' => fn ($q) => $q->where('status', Absensi::STATUS_SAKIT)->when($this->start, fn ($q) => $q->whereDate('tanggal', '>=', $this->start))->when($this->end, fn ($q) => $q->whereDate('tanggal', '<=', $this->end)),
                'absensi as total_izin' => fn ($q) => $q->where('status', Absensi::STATUS_IZIN)->when($this->start, fn ($q) => $q->whereDate('tanggal', '>=', $this->start))->when($this->end, fn ($q) => $q->whereDate('tanggal', '<=', $this->end)),
                'absensi as total_alfa' => fn ($q) => $q->where('status', Absensi::STATUS_ALFA)->when($this->start, fn ($q) => $q->whereDate('tanggal', '>=', $this->start))->when($this->end, fn ($q) => $q->whereDate('tanggal', '<=', $this->end)),
            ])
            ->when($searchTerms !== [], function ($query) use ($searchTerms) {
                $query->where(function ($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->orWhereRaw('LOWER(nama_lengkap) LIKE ?', ["%{$term}%"])
                            ->orWhereRaw('LOWER(nim) LIKE ?', ["%{$term}%"])
                            ->orWhereRaw('LOWER(id_anggota) LIKE ?', ["%{$term}%"]);
                    }
                });
            })
            ->orderBy('id_anggota');
    }

    public function headings(): array
    {
        return [
            'No',
            'NAMA ANGGOTA',
            'NIM',
            'TOTAL SAKIT',
            'TOTAL IZIN',
            'TOTAL ALFA',
            'TOTAL HADIR',
        ];
    }

    public function map($row): array
    {
        $this->index++;

        return [
            $this->index,
            $row->nama_lengkap,
            $row->nim,
            (string) $row->total_sakit,
            (string) $row->total_izin,
            (string) $row->total_alfa,
            (string) $row->total_hadir,
        ];
    }
}
