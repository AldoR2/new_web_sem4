<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['presensiBerhasil', 'presensiGagal', 'presensiAkanHabis', 'pengumuman']); // bisa ditambah jika ada jenis lain
            $table->string('nama_user');
            $table->string('tanggal');
            $table->string('jam');
            $table->string('mata_kuliah')->nullable();
            $table->foreignId('presensi_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
