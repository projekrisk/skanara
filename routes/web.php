<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\RegisterSchoolController;
use App\Http\Controllers\DownloadTemplateController;
use App\Http\Controllers\DownloadQrController;
use App\Http\Controllers\ExportJurnalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Halaman Depan (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

// 2. Halaman Kebijakan Privasi (Wajib untuk Play Store)
Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('privacy.policy');

// 3. Proses Pendaftaran Sekolah Baru
Route::post('/register-school', [RegisterSchoolController::class, 'store'])->name('register.school');

// 4. Utilitas: Migrasi Database (Opsional, hapus di production)
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
    // Harian (Per ID Jurnal)
    Route::get('/export-jurnal/{id}', [ExportJurnalController::class, 'export'])
        ->name('export.jurnal');
        
    // Bulanan (Rekap)
    Route::get('/export-jurnal-bulanan', [ExportJurnalController::class, 'exportBulanan'])
        ->name('export.jurnal.bulanan');
        
    // D. Test Manual Scheduler (Opsional)
    Route::get('/test-autocancel', function () {
        Artisan::call('tagihan:autocancel');
        return "Command Autocancel dijalankan.";
    });
});