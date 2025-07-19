<?php

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Mahasiswa\DashboardController;
use App\Http\Controllers\Mahasiswa\MahasiswaController;
use App\Http\Controllers\Mahasiswa\PresensiController;
use App\Http\Controllers\Mahasiswa\JadwalController;
use App\Http\Controllers\Mahasiswa\RekapPresensiController;

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::resource('presensi', PresensiController::class);
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::get('/jadwal',[JadwalController::class,'index'])->name('jadwal');
    Route::get('/jadwal/export/pdf', [JadwalController::class, 'exportJadwalPdf'])->name('export.jadwal.pdf');
    Route::get('/jadwal/export/excel', [JadwalController::class, 'exportJadwalExcel'])->name('export.jadwal.excel');
    Route::get('/getFilterJadwal', [JadwalController::class, 'getFilterJadwal']);

    Route::get('/rekap-presensi',[RekapPresensiController::class,'index'])->name('rekap');
    Route::get('/rekap-presensi/export/pdf', [RekapPresensiController::class, 'exportRekapPdf'])->name('export.mahasiswa.pdf');
    Route::get('/rekap-presensi/export/excel', [RekapPresensiController::class, 'exportRekapExcel'])->name('export.mahasiswa.excel');

    Route::get('/change-password', [PasswordController::class, 'changePassword'])->name('change-password');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');
    Route::get('/getFilterRekap', [MahasiswaController::class, 'getFilterRekap']);
    Route::put('/update-profil', [MahasiswaController::class, 'updateProfil'])->name('profil.update');
});
