<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\RegisterSchoolController;
use App\Http\Controllers\DownloadTemplateController;
use App\Http\Controllers\DownloadQrController;
use App\Http\Controllers\ExportJurnalController;
use App\Http\Controllers\CetakRiwayatController; // Import Controller Baru
use App\Http\Controllers\LaporanAbsensiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Halaman Depan (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

// 2. Halaman Kebijakan Privasi
Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('privacy.policy');

// 3. Proses Pendaftaran Sekolah
Route::post('/register-school', [RegisterSchoolController::class, 'store'])->name('register.school');

// 4. Migrasi Database (Opsional)
Route::get('/migrate-force', function() {
    Artisan::call('migrate --force');
    return 'Database Migration Completed.';
});

// --- ROUTE YANG MEMBUTUHKAN LOGIN (AUTH) ---
Route::middleware('auth')->group(function () {
    
    // A. Download Template Excel (Import Siswa)
    Route::get('/download-template-siswa', [DownloadTemplateController::class, 'downloadTemplateSiswa'])
        ->name('download.template.siswa');

    // B. Download QR Code Massal (ZIP)
    Route::get('/download-qr-zip', [DownloadQrController::class, 'download'])
        ->name('download.qr.zip');

    // C. Export Jurnal Mengajar (Excel)
    Route::get('/export-jurnal/{id}', [ExportJurnalController::class, 'export'])
        ->name('export.jurnal');
    Route::get('/export-jurnal-bulanan', [ExportJurnalController::class, 'exportBulanan'])
        ->name('export.jurnal.bulanan');
        
    // D. Cetak Riwayat Siswa (PDF) - YANG DIBUTUHKAN SAAT INI
    Route::get('/cetak-riwayat-siswa', [CetakRiwayatController::class, 'cetak'])
        ->name('cetak.riwayat.siswa');
        
    // E. Test Scheduler (Opsional)
    Route::get('/test-autocancel', function () {
        Artisan::call('tagihan:autocancel');
        return "Command Autocancel dijalankan.";
    });

    Route::get('/download-laporan-absensi', [LaporanAbsensiController::class, 'download'])
        ->name('download.laporan.absensi');
});