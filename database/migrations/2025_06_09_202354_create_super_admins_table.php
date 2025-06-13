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
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nama','100');
            $table->char('jenis_kelamin','1');
            $table->string('agama','20');
            $table->string('tempat_lahir','100');
            $table->date('tgl_lahir');
            $table->string('email')->unique();
            $table->string('no_telp','20');
            $table->string('alamat');
            $table->string('foto','100')->nullable();
            $table->char('provinsi_id', 2); // ← pastikan ini ADA sebelum foreign()
            $table->char('kota_id', 4); // ← pastikan ini ADA sebelum foreign()
            $table->char('kecamatan_id', 7); // ← pastikan ini ADA sebelum foreign()
            $table->char('kelurahan_id', 10); // ← pastikan ini ADA sebelum foreign()

            $table->foreign('provinsi_id')->references('id')->on('provinsis');
            $table->foreign('kota_id')->references('id')->on('kotas');
            $table->foreign('kecamatan_id')->references('id')->on('kecamatans');
            $table->foreign('kelurahan_id')->references('id')->on('kelurahans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admins');
    }
};
