<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pertemuans', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('pertemuan_ke'); // 1-16
            $table->enum('status', ['aktif', 'libur', 'uts', 'uas'])->default('aktif');
            $table->foreignId('matkul_id')->constrained('matkuls');
            $table->foreignId('prodi_id')->constrained('prodis');
            $table->tinyInteger('semester');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertemuans');
    }
};
