<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    public const STATUS_HADIR = 'hadir';

    public const STATUS_IZIN = 'izin';

    public const STATUS_SAKIT = 'sakit';

    public const STATUS_ALFA = 'alfa';

    public const SUMBER_SCAN = 'scan';

    public const SUMBER_MANUAL = 'manual';

    public const SUMBER_OTOMATIS = 'otomatis';

    public const SUMBER_IZIN_DISETUJUI = 'izin_disetujui';

    protected $fillable = [
        'anggota_id',
        'jadwal_id',
        'petugas_id',
        'izin_sakit_id',
        'tanggal',
        'status',
        'sumber',
        'waktu_scan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu_scan' => 'datetime',
        ];
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function izinSakit(): BelongsTo
    {
        return $this->belongsTo(IzinSakit::class);
    }
}
