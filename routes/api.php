<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KioskAuthController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\AbsensiKelasController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/kiosk/login', [KioskAuthController::class, 'login']);
// Check config juga bisa diakses via API
Route::get('/check-config', [SyncController::class, 'checkConfig']);

// --- PROTECTED ROUTES (Butuh Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // User Info & Logout
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Fitur Sinkronisasi (Kiosk & Dashboard)
    Route::get('/sync/master', [SyncController::class, 'getMasterData']);
    Route::get('sync/presensi-today', [SyncController::class, 'getPresensiToday']);
    Route::post('/sync/upload', [SyncController::class, 'uploadPresensi']);

    // Fitur Guru (Absensi Kelas)
    Route::post('/absensi-kelas/store', [AbsensiKelasController::class, 'store']);
    Route::get('/absensi-kelas/today', [AbsensiKelasController::class, 'getHistoryToday']);
    Route::get('/absensi-kelas/cek-status', [\App\Http\Controllers\Api\AbsensiKelasController::class, 'cekStatusPerKelas']);
    Route::get('/laporan/guru-bulanan', [\App\Http\Controllers\Api\SyncController::class, 'getLaporanBulananGuru']);
    
    
    // Fitur Laporan Mobile (JSON Data untuk Android)
    // Rute ini dipindah ke sini karena membutuhkan autentikasi token guru
    Route::get('/laporan/harian-mobile', [SyncController::class, 'getLaporanHarianMobile']);
    
});