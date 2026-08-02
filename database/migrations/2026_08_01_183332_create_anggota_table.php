<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('id_anggota')->unique();
            $table->string('nama_lengkap');
            $table->string('nim');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('no_whatsapp')->nullable();
            $table->string('foto_dobok')->nullable();
            $table->string('fakultas');
            $table->string('program_studi');
            $table->string('no_bpjs')->nullable();
            $table->string('qr_code');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
