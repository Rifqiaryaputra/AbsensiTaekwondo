<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanProfil extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_profil';

    protected $fillable = [
        'logo_unit_kegiatan',
        'logo_universitas',
        'nama_unit_kegiatan',
        'nama_universitas',
        'alamat_sekretariat',
    ];

    public static function instance(): self
    {
        return self::query()->firstOrCreate(['id' => 1]);
    }
}
