<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Mahasiswa\MahasiswaController;
use App\Http\Controllers\Mahasiswa\PresensiController;

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::resource('presensi', PresensiController::class);
    Route::get('/dashboard',[DashboardController::class,'indexMahasiswa'])->name('dashboard');
    Route::get('/jadwal',[MahasiswaController::class,'jadwal'])->name('jadwal');
    Route::get('/rekap-presensi',[MahasiswaController::class,'rekap'])->name('rekap');

    Route::get('/rekap-presensi/export/pdf', [MahasiswaController::class, 'exportPdf'])->name('export.mahasiswa.pdf');
    Route::get('/rekap-presensi/export/excel', [MahasiswaController::class, 'exportExcel'])->name('export.mahasiswa.excel');

    Route::get('/change-password', [PasswordController::class, 'changePassword'])->name('change-password');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');
    Route::get('/getFilterRekap', [MahasiswaController::class, 'getFilterRekap']);
    Route::put('/update-profil', [MahasiswaController::class, 'updateProfil'])->name('profil.update');
    Route::get('/getFilterJadwal', [MahasiswaController::class, 'getFilterJadwal']);
});
