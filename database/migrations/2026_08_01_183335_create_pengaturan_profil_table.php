<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_profil', function (Blueprint $table) {
            $table->id();
            $table->string('logo_unit_kegiatan')->nullable();
            $table->string('logo_universitas')->nullable();
            $table->string('nama_unit_kegiatan')->nullable();
            $table->string('nama_universitas')->nullable();
            $table->string('alamat_sekretariat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_profil');
    }
};
