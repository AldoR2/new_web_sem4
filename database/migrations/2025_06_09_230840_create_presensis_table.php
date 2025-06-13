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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->string('presensi_id', '30');
            $table->foreignId('pertemuan_id')->constrained('pertemuans');
            $table->date('tgl_presensi');
            $table->time('jam_awal');
            $table->time('jam_akhir');
            $table->foreignId('dosen_id')->constrained('dosens');
            $table->foreignId('ruangan_id')->constrained('ruangans')->nullable();
            $table->string('link_zoom', '255')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
