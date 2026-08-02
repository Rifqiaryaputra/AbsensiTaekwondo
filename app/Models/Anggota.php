<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $fillable = [
        'id_anggota',
        'nama_lengkap',
        'nim',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_whatsapp',
        'foto_dobok',
        'fakultas',
        'program_studi',
        'no_bpjs',
        'qr_code',
    ];

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

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function izinSakit(): HasMany
    {
        return $this->hasMany(IzinSakit::class);
    }
}
