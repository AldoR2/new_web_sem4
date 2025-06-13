<?php

use App\Http\Controllers\SuperAdmin\AdminController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // Route::get('/dashboard',[DashboardController::class,'indexSuperAdmin'])->name('dashboard');
    Route::resource('master-admin', AdminController::class);
    Route::post('/validate-field/admin', [AdminController::class, 'validateField'])->name('admin.validate.field.admin');
    Route::post('/master-admin/import', [AdminController::class, 'import'])->name('master-admin.import');
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/change-password', [PasswordController::class, 'changePassword'])->name('change-password');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/validate-field/password', [PasswordController::class, 'validateField'])->name('admin.validate.field.password');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
