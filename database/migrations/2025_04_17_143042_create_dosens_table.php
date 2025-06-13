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
        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nip','20')->unique();
            $table->string('nama','100');
            $table->char('jenis_kelamin','1');
            $table->string('agama','20');
            $table->string('tempat_lahir','100');
            $table->date('tgl_lahir');
            $table->string('email')->unique();
            $table->string('no_telp','20');
            $table->string('alamat');
            $table->foreignId('prodi_id')->constrained('prodis');
            $table->string('foto','100')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};
