<?php

namespace App\Livewire;

use App\Models\Absensi;
use Carbon\Carbon;
use Livewire\Component;

class RekapKehadiran extends Component
{
    public string $start;

    public string $end;

    public function mount(): void
    {
        $this->start = Carbon::now()->startOfMonth()->toDateString();
        $this->end = Carbon::now()->endOfMonth()->toDateString();
    }

    public function updatedStart(): void
    {
        $this->normalizeRange();
    }

    public function updatedEnd(): void
    {
        $this->normalizeRange();
    }

    private function normalizeRange(): void
    {
        if ($this->start !== '' && $this->end !== '' && $this->start > $this->end) {
            $this->start = $this->end;
        }
    }

    public function data(): \Illuminate\Database\Eloquent\Collection
    {
        return Absensi::query()
            ->with('anggota')
            ->whereDate('tanggal', '>=', $this->start)
            ->whereDate('tanggal', '<=', $this->end)
            ->orderBy('tanggal')
            ->orderBy('anggota_id')
            ->get();
    }

    public function summary(): array
    {
        $counts = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alfa' => 0];

        foreach ($this->data() as $row) {
            $counts[$row->status] = ($counts[$row->status] ?? 0) + 1;
        }

        return $counts;
    }

    public function formatTanggal(string $date): string
    {
        return Carbon::parse($date)->translatedFormat('l, d M Y');
    }

    public function formatShort(string $date): string
    {
        return Carbon::parse($date)->translatedFormat('d M Y');
    }

    public function exportUrl(): string
    {
        return route('rekap.export', ['start' => $this->start, 'end' => $this->end]);
    }

    public function render()
    {
        return view('livewire.rekap-kehadiran', [
            'data' => $this->data(),
            'summary' => $this->summary(),
        ]);
    }
}
