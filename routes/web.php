<?php

use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TemplateSiswaExport;

// Controllers
use App\Http\Controllers\Auth\RegisterSekolahController;
use App\Http\Controllers\LaporanSiswaController;
use App\Http\Controllers\LaporanPresensiController;
use App\Http\Controllers\LaporanAbsensiKelasController;
use App\Http\Controllers\LaporanCetakController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\CetakKartuController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Halaman Depan (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

// --- PENDAFTARAN SEKOLAH ---
Route::get('/register-sekolah', [RegisterSekolahController::class, 'show'])->name('register.sekolah');
Route::post('/register-sekolah', [RegisterSekolahController::class, 'store'])->name('register.sekolah.store');

// Halaman Pemberitahuan "Cek Email"
Route::get('/register/notice', [RegisterSekolahController::class, 'notice'])->name('register.notice');

// Verifikasi Email (Link dari Email akan mengarah ke sini)
Route::get('/register/verify/{token}', [RegisterSekolahController::class, 'verify'])->name('register.verify');
Route::get('/cetak-kartu', [CetakKartuController::class, 'print'])->name('cetak.kartu');

// --- DOWNLOAD UTILITIES ---
Route::get('/download-template-siswa', function () {
    return Excel::download(new TemplateSiswaExport, 'template_siswa.xlsx');
})->name('download.template.siswa');


// --- CETAK LAPORAN (HTML/PDF) ---

// 1. Laporan Individu Siswa
Route::get('/laporan/siswa/{id}', [LaporanSiswaController::class, 'print'])->name('cetak.laporan.siswa');
// 2. Laporan Rekap Presensi Umum
Route::get('/laporan/presensi', [LaporanPresensiController::class, 'print'])->name('laporan.presensi.print');

// 3. Laporan Jurnal Absensi Kelas (Guru)
Route::get('/laporan/absensi-kelas', [LaporanAbsensiKelasController::class, 'print'])->name('laporan.absensi-kelas.print');

// 4. Laporan Terpusat (Harian & Bulanan)
Route::get('/laporan-cetak', [LaporanCetakController::class, 'print'])->name('laporan.presensi.cetak');

// 5. Laporan Harian Per Kelas (Tombol di Tabel Presensi)
Route::get('/laporan/harian-kelas', [LaporanCetakController::class, 'printHarianPerKelas'])->name('laporan.presensi.harian.kelas');


// --- DIAGNOSA ---
// Endpoint ini aman diakses via browser untuk cek data JSON konfigurasi sekolah
Route::get('/check-config', [SyncController::class, 'checkConfig']);


// --- FIX ERROR "Route [login] not defined" ---
// Laravel secara default mencari route bernama 'login' jika user belum terautentikasi.
// Kita arahkan paksa ke halaman login Filament Sekolah.
Route::get('/login', function () {
    return redirect()->route('filament.sekolah.auth.login');
})->name('login');