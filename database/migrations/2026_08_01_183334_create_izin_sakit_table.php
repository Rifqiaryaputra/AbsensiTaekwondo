<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_sakit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota');
            $table->foreignId('jadwal_id')->constrained('jadwal');
            $table->date('tanggal');
            $table->enum('jenis', ['izin', 'sakit']);
            $table->text('keterangan');
            $table->string('bukti_lampiran')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dibatalkan'])->default('menunggu');
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diajukan_pada')->useCurrent();
            $table->timestamp('diproses_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_sakit');
    }
};
