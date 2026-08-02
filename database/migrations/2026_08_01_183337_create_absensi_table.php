<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota');
            $table->foreignId('jadwal_id')->constrained('jadwal');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('izin_sakit_id')->nullable()->constrained('izin_sakit')->nullOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa']);
            $table->enum('sumber', ['scan', 'manual', 'otomatis', 'izin_disetujui']);
            $table->timestamp('waktu_scan')->nullable();
            $table->timestamps();

            $table->unique(['anggota_id', 'jadwal_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
