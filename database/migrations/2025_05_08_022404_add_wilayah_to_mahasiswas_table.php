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
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->char('provinsi_id', 2); // ← pastikan ini ADA sebelum foreign()
            $table->char('kota_id', 4); // ← pastikan ini ADA sebelum foreign()
            $table->char('kecamatan_id', 7); // ← pastikan ini ADA sebelum foreign()
            $table->char('kelurahan_id', 10); // ← pastikan ini ADA sebelum foreign()

            $table->foreign('provinsi_id')->references('id')->on('provinsis');
            $table->foreign('kota_id')->references('id')->on('kotas');
            $table->foreign('kecamatan_id')->references('id')->on('kecamatans');
            $table->foreign('kelurahan_id')->references('id')->on('kelurahans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            //
        });
    }
};
