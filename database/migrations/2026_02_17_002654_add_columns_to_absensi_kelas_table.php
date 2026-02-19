<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensi_kelas', function (Blueprint $table) {
            // Cek satu per satu agar tidak error jika sebagian sudah ada
            
            if (!Schema::hasColumn('absensi_kelas', 'waktu_input')) {
                $table->time('waktu_input')->nullable()->after('tanggal');
            }
            
            // Kolom Statistik
            if (!Schema::hasColumn('absensi_kelas', 'jumlah_hadir')) {
                $table->integer('jumlah_hadir')->default(0)->after('waktu_input');
            }
            if (!Schema::hasColumn('absensi_kelas', 'jumlah_sakit')) {
                $table->integer('jumlah_sakit')->default(0)->after('jumlah_hadir');
            }
            if (!Schema::hasColumn('absensi_kelas', 'jumlah_izin')) {
                $table->integer('jumlah_izin')->default(0)->after('jumlah_sakit');
            }
            if (!Schema::hasColumn('absensi_kelas', 'jumlah_alpa')) {
                $table->integer('jumlah_alpa')->default(0)->after('jumlah_izin');
            }
            
            if (!Schema::hasColumn('absensi_kelas', 'catatan')) {
                $table->text('catatan')->nullable()->after('jumlah_alpa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_kelas', function (Blueprint $table) {
            $table->dropColumn([
                'waktu_input', 
                'jumlah_hadir', 
                'jumlah_sakit', 
                'jumlah_izin', 
                'jumlah_alpa', 
                'catatan'
            ]);
        });
    }
};