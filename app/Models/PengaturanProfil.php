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
        // Selalu pakai baris pertama (singleton). Hanya buat id=1 bila tabel masih kosong.
        $row = self::query()->first();

        if (! $row) {
            $row = new self;
            $row->id = 1;
            $row->save();
        }

        return $row;
    }
}
