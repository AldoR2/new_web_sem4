<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KalenderAkademikController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Mahasiswa\MahasiswaController;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->get('/dashboard', function () {
    $role = auth()->user()->role;

    return match($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'dosen' => redirect()->route('dosen.dashboard'),
        'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
        'superadmin' => redirect()->route('superadmin.dashboard'),
        default => abort(403),
    };
});

Route::middleware(['auth'])->get('/master-admin', function () {
    redirect()->route('superadmin.master-admin.index');

});


Route::get('/rfid/presensi',[MahasiswaController::class,'prosesPresensi']);

Route::get('/getMatkulByProdi', [PresensiController::class, 'getMatkulByProdi']);
Route::get('/kalender-akademik', [KalenderAkademikController::class, 'viewCalendar'])->name('kalender-akademik.view');

Route::get('/wilayah/{type}/{id?}', function ($type, $id = null) {
    return match ($type) {
        'provinsis' => Provinsi::select('id', 'name')->get(),
        'kotas' => Kota::where('provinsi_id', $id)->select('id', 'name')->get(),
        'kecamatans' => Kecamatan::where('kota_id', $id)->select('id', 'name')->get(),
        'kelurahans' => Kelurahan::where('kecamatan_id', $id)->select('id', 'name')->get(),
        default => abort(404),
    };
});

require __DIR__.'/auth.php';
require __DIR__.'/superadmin.php';
require __DIR__.'/admin.php';
require __DIR__.'/dosen.php';
require __DIR__.'/mahasiswa.php';
