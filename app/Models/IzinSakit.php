<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IzinSakit extends Model
{
    use HasFactory;

    protected $table = 'izin_sakit';

    public const JENIS_IZIN = 'izin';

    public const JENIS_SAKIT = 'sakit';

    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    protected $fillable = [
        'anggota_id',
        'jadwal_id',
        'tanggal',
        'jenis',
        'keterangan',
        'bukti_lampiran',
        'status',
        'diproses_oleh',
        'diajukan_pada',
        'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'diajukan_pada' => 'datetime',
            'diproses_pada' => 'datetime',
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

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
