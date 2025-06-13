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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->time('jam');
            $table->char('durasi','2');
            $table->enum('hari',['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']);
            $table->foreignId('dosen_id')->constrained('dosens');
            $table->foreignId('prodi_id')->constrained('prodis');
            $table->foreignId('matkul_id')->constrained('matkuls');
            $table->foreignId('ruangan_id')->constrained('ruangans');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans');
            $table->tinyInteger('semester');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
