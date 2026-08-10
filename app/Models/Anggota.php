<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_NON_AKTIF = 'non-aktif';

    public const STATUS_ALUMNI = 'alumni';

    protected $fillable = [
        'id_anggota',
        'nama_lengkap',
        'nim',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_whatsapp',
        'foto_dobok',
        'fakultas_id',
        'program_studi_id',
        'no_bpjs',
        'status_anggota',
        'qr_code',
    ];

    public static function statusList(): array
    {
        return [
            self::STATUS_AKTIF,
            self::STATUS_NON_AKTIF,
            self::STATUS_ALUMNI,
        ];
    }

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user(): HasOne
    {
        // Selalu kembalikan akun ANGGOTA (bukan akun petugas terpisah yang mungkin dibuat).
        return $this->hasOne(User::class)->where('role', User::ROLE_ANGGOTA);
    }

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function izinSakit(): HasMany
    {
        return $this->hasMany(IzinSakit::class);
    }
}
