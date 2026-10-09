<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileDashboardController;
use App\Http\Controllers\Api\MobileAbsensiStaffController;

Route::post('/login', [
    MobileAuthController::class,
    'login',
]);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [
        MobileAuthController::class,
        'logout',
    ]);

    Route::get('/dashboard', [
        MobileDashboardController::class,
        'index',
    ]);

    // LOKASI ABSENSI
    Route::get('/absensi-staff/lokasi', [
        MobileAbsensiStaffController::class,
        'lokasi',
    ]);

    // ABSEN MASUK
    Route::post('/absensi-staff/masuk', [
        MobileAbsensiStaffController::class,
        'masuk',
    ]);

    // ABSEN PULANG
    Route::post('/absensi-staff/pulang', [
        MobileAbsensiStaffController::class,
        'pulang',
    ]);
});