<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mengubah kolom 'metode' menjadi VARCHAR(50) agar bisa menerima input 'kiosk_offline', 'scan', dll.
        // Kita gunakan DB::statement agar aman tanpa perlu install doctrine/dbal
        
        // Cek driver database, jika MySQL:
        if (config('database.default') === 'mysql') {
             DB::statement("ALTER TABLE presensi MODIFY COLUMN metode VARCHAR(50) NULL DEFAULT 'manual'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu dikembalikan ke ENUM agar data tidak rusak
    }
};