<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            // Kita gunakan JSON agar bisa menyimpan array [6, 7] dsb.
            // Default null (nanti di model kita set defaultnya)
            $table->json('hari_libur_mingguan')->nullable()->after('nama_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn('hari_libur_mingguan');
        });
    }
};