<?php

use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\KalenderAkademikController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\MatkulController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\RekapPresensi\RekapDosenAdminController;
use App\Http\Controllers\RekapPresensi\RekapMahasiswaController;
use App\Http\Controllers\Admin\RuanganController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\RekapPresensi\RekapMatkulController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('kalender-akademik', KalenderAkademikController::class)
    ->except(['show']);
    Route::get('kalender-akademik/view', [KalenderAkademikController::class, 'viewCalendar'])->name('kalender-akademik.view');
    Route::post('/validate-field/kalender-akademik', [KalenderAkademikController::class, 'validateField'])->name('admin.validate.field.kalender');

    Route::get('/dashboard',[DashboardController::class,'indexAdmin'])->name('dashboard');

    Route::resource('master-dosen', DosenController::class);
    Route::get('/api/filter-data', [DosenController::class, 'filter']);
    Route::post('/validate-field/dosen', [DosenController::class, 'validateField'])->name('admin.validate.field.dosen');
    Route::post('/master-dosen/import', [DosenController::class, 'import'])->name('master-dosen.import');

    Route::resource('master-mahasiswa', MahasiswaController::class);
    Route::post('/validate-field/mahasiswa', [MahasiswaController::class, 'validateField'])->name('admin.validate.field.mahasiswa');
    Route::get('/getFilterMahasiswa', [MahasiswaController::class, 'getFilterMahasiswa']);
    Route::post('/master-mahasiswa/import', [MahasiswaController::class, 'import'])->name('master-mahasiswa.import');

    Route::resource('master-tahun', TahunAjaranController::class);
    Route::post('/validate-field/tahun', [TahunAjaranController::class, 'validateField'])->name('admin.validate.field.tahun');

    Route::resource('master-prodi', ProdiController::class);
    Route::get('/api/prodi', [ProdiController::class, 'getList']);
    Route::post('/validate-field/prodi', [ProdiController::class, 'validateField'])->name('admin.validate.field.prodi');

    Route::resource('master-matkul', MatkulController::class);
    Route::post('/validate-field/matkul', [MatkulController::class, 'validateField'])->name('admin.validate.field.matkul');
    Route::get('/getFilterMatkul', [MatkulController::class, 'getFilterMatkul']);

    Route::resource('master-ruangan', RuanganController::class);
    Route::post('/validate-field/ruangan', [RuanganController::class, 'validateField'])->name('admin.validate.field.ruangan');

    Route::resource('master-jadwal', JadwalController::class);
    Route::post('/validate-field/jadwal', [JadwalController::class, 'validateField'])->name('admin.validate.field.jadwal');
    Route::get('/getMatkulByTahun', [JadwalController::class, 'getMatkulByTahun']);
    Route::get('/getFilterJadwal', [JadwalController::class, 'getFilterJadwal']);


    Route::resource('presensi', PresensiController::class);
    Route::post('/presensi/info-presensi', [PresensiController::class, 'updateDetailPresensi'])
    ->name('update-detail-presensi');
    Route::get('/presensi/info-presensi',function(){
        return view('admin.info-presensi',['title'=> 'Dashboard', 'rute'=> 'admin -> Dashboard']);
    })->name('info-presensi');
    Route::post('/validate-field/presensi', [PresensiController::class, 'validateField'])->name('admin.validate.field.presensi');

    Route::resource('rekap-dosen', RekapDosenAdminController::class);
    Route::post('rekap-dosen', [RekapDosenAdminController::class, 'rekapDosen'])->name('rekap-dosen.filter');
    Route::post('/rekap-dosen/export/pdf', [RekapDosenAdminController::class, 'exportPdf'])->name('export.dosen.pdf');
    Route::post('/rekap-dosen/export/excel', [RekapDosenAdminController::class, 'exportExcel'])->name('export.dosen.excel');

    Route::resource('rekap-mahasiswa', RekapMahasiswaController::class);
    Route::post('rekap-mahasiswa', [RekapMahasiswaController::class, 'rekapMahasiswa'])->name('rekap-mahasiswa.filter');
    Route::post('/rekap-mahasiswa/export/pdf', [RekapMahasiswaController::class, 'exportPdf'])->name('export.mahasiswa.pdf');
    Route::post('/rekap-mahasiswa/export/excel', [RekapMahasiswaController::class, 'exportExcel'])->name('export.mahasiswa.excel');

    Route::resource('rekap-matkul', RekapMatkulController::class);
    Route::post('rekap-matkul', [RekapMatkulController::class, 'rekapMatkul'])->name('rekap-matkul.filter');
    Route::post('/rekap-matkul/export/pdf', [RekapMatkulController::class, 'exportPdf'])->name('export.matkul.pdf');
    Route::post('/rekap-matkul/export/excel', [RekapMatkulController::class, 'exportExcel'])->name('export.matkul.excel');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/change-password', [PasswordController::class, 'changePassword'])->name('change-password');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/validate-field/password', [PasswordController::class, 'validateField'])->name('admin.validate.field.password');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
