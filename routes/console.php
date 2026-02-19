<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Transaksi;

// Command bawaan Laravel (Test apakah scheduler berjalan)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/**
 * Scheduler: Pembatalan Transaksi Otomatis
 * Logika ini akan berjalan di background setiap jam JIKA Cron Job server aktif.
 * Jika server tidak support Cron, logika ini tidak akan jalan (tapi sudah di-handle oleh ProfilSekolah.php).
 */
Schedule::call(function () {
    // Update semua transaksi 'menunggu' yang dibuat lebih dari 24 jam lalu menjadi 'ditolak'
    $affected = Transaksi::where('status', 'menunggu')
        ->where('created_at', '<', now()->subHours(24))
        ->update(['status' => 'ditolak']);

    if ($affected > 0) {
        // Log ke file laravel.log jika ada yang dibatalkan
        info("Scheduler: Membatalkan {$affected} transaksi kadaluarsa.");
    }
})->hourly();