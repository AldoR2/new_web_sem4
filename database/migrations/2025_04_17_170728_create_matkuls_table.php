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
        Schema::create('matkuls', function (Blueprint $table) {
            $table->id();
            $table->string('kode_matkul','15')->unique();
            $table->string('nama_matkul','100');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans');
            $table->tinyInteger('semester');
            $table->char('durasi_matkul','2');
            $table->foreignId('prodi_id')->constrained('prodis');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matkuls');
    }
};
