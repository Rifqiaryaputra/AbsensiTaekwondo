<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->foreignId('fakultas_id')->nullable()->constrained('fakultas')->nullOnDelete();
            $table->foreignId('program_studi_id')->nullable()->constrained('program_studi')->nullOnDelete();
        });

        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['fakultas', 'program_studi']);
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropConstrainedForeignId(['fakultas_id', 'program_studi_id']);
            $table->string('fakultas')->nullable();
            $table->string('program_studi')->nullable();
        });
    }
};
