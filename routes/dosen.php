<?php

use App\Http\Controllers\Admin\KalenderAkademikController;
// use App\Http\Controllers\Dosen\RekapDosenController;
use App\Http\Controllers\RekapPresensi\RekapDosenDosenController;
use App\Http\Controllers\RekapPresensi\RekapMahasiswaController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dosen\JadwalController;
use App\Http\Controllers\Dosen\PresensiController;
use App\Http\Controllers\Dosen\ProfileController;
use App\Http\Controllers\RekapPresensi\RekapMatkulController;
use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/presensi',[PresensiController::class,'index'])->name('presensi');

    Route::post('/validate-field/kalender-akademik', [KalenderAkademikController::class, 'validateField'])->name('admin.validate.field.kalender');

    Route::get('/dashboard',[DashboardController::class,'indexDosen'])->name('dashboard');

    Route::get('/jadwal',[JadwalController::class,'index'])->name('jadwal');
    Route::get('/jadwal/export/pdf', [JadwalController::class, 'exportPdf'])->name('export.jadwal.pdf');
    Route::get('/jadwal/export/excel', [JadwalController::class, 'exportExcel'])->name('export.jadwal.excel');
    Route::get('/getFilterJadwal', [JadwalController::class, 'getFilterJadwal']);

    Route::get('/rekap-dosen/export/pdf', [RekapDosenDosenController::class, 'exportPdf'])->name('export.dosen.pdf');
    Route::get('/rekap-dosen/export/excel', [RekapDosenDosenController::class, 'exportExcel'])->name('export.dosen.excel');

    Route::post('/rekap-mahasiswa/export/pdf', [RekapMahasiswaController::class, 'exportPdf'])->name('export.mahasiswa.pdf');
    Route::post('/rekap-mahasiswa/export/excel', [RekapMahasiswaController::class, 'exportExcel'])->name('export.mahasiswa.excel');

    Route::resource('presensi', PresensiController::class);
    Route::post('/presensi/info-presensi', [PresensiController::class, 'updateDetailPresensi'])
    ->name('update-detail-presensi');

    Route::post('/validate-field/presensi', [PresensiController::class, 'validateField'])->name('admin.validate.field.presensi');

    Route::resource('rekap-dosen', RekapDosenDosenController::class);
    Route::post('rekap-dosen', [RekapDosenDosenController::class, 'rekapDosen'])->name('rekap-dosen.filter');
    Route::get('/getFilterRekap', [RekapDosenDosenController::class, 'getFilterRekap']);

    Route::resource('rekap-mahasiswa', RekapMahasiswaController::class);
    Route::post('rekap-mahasiswa', [RekapMahasiswaController::class, 'rekapMahasiswa'])->name('rekap-mahasiswa.filter');
    Route::get('/getMatkulDosen', [RekapMahasiswaController::class, 'getMatkulDosen']);

    Route::resource('rekap-matkul', RekapMatkulController::class);
    Route::post('rekap-matkul', [RekapMatkulController::class, 'rekapMatkul'])->name('rekap-matkul.filter');
    Route::post('/rekap-matkul/export/pdf', [RekapMatkulController::class, 'exportPdf'])->name('export.matkul.pdf');
    Route::post('/rekap-matkul/export/excel', [RekapMatkulController::class, 'exportExcel'])->name('export.matkul.excel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/change-password', [PasswordController::class, 'changePassword'])->name('change-password');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/validate-field/change-password', [ProfileController::class, 'validateField'])->name('dosen.validate.field.profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
