<?php

use App\Http\Controllers\SuperAdmin\AdminController;
use App\Http\Controllers\Auth\PasswordController;

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::resource('master-admin', AdminController::class);
    Route::post('/validate-field/admin', [AdminController::class, 'validateField'])->name('admin.validate.field.admin');
    Route::post('/master-admin/import', [AdminController::class, 'import'])->name('master-admin.import');
    Route::get('/change-password', [PasswordController::class, 'changePassword'])->name('change-password');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/validate-field/password', [PasswordController::class, 'validateField'])->name('admin.validate.field.password');
});
