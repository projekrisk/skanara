<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan opsi 'diproses' ke dalam kolom ENUM status
        // Syntax ini khusus MySQL
        DB::statement("ALTER TABLE transaksi MODIFY COLUMN status ENUM('menunggu', 'diproses', 'disetujui', 'ditolak') DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        // Kembalikan ke kondisi semula (Hati-hati jika ada data 'diproses')
        // DB::statement("ALTER TABLE transaksi MODIFY COLUMN status ENUM('menunggu', 'disetujui', 'ditolak') DEFAULT 'menunggu'");
    }
};
