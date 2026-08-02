<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'hari',
        'jam_start',
        'jam_close',
    ];

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function izinSakit(): HasMany
    {
        return $this->hasMany(IzinSakit::class);
    }

    public function petugas(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'jadwal_petugas')->withTimestamps();
    }
}
