<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_kelas', function (Blueprint $table) {
            // Kolom JSON untuk menyimpan array siswa yang tidak hadir (id, status, ket)
            $table->json('detail_kehadiran')->nullable()->after('jumlah_alpa');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_kelas', function (Blueprint $table) {
            $table->dropColumn('detail_kehadiran');
        });
    }
};