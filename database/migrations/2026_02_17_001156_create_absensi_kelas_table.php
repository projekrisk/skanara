<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek jika tabel belum ada, buat baru
        if (!Schema::hasTable('absensi_kelas')) {
            Schema::create('absensi_kelas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_sekolah')->constrained('sekolah')->cascadeOnDelete();
                $table->foreignId('id_guru')->constrained('users')->cascadeOnDelete(); // Guru yang menginput
                $table->foreignId('id_kelas')->constrained('kelas')->cascadeOnDelete();
                
                $table->date('tanggal');
                $table->time('waktu_input'); // Kolom Jam
                
                // Statistik Kehadiran
                $table->integer('jumlah_hadir')->default(0);
                $table->integer('jumlah_sakit')->default(0);
                $table->integer('jumlah_izin')->default(0);
                $table->integer('jumlah_alpa')->default(0);
                
                $table->text('catatan')->nullable();
                
                $table->timestamps();
            });
        } 
        else {
            // Jika tabel sudah ada tapi kolomnya kurang, tambahkan di sini
            Schema::table('absensi_kelas', function (Blueprint $table) {
                if (!Schema::hasColumn('absensi_kelas', 'waktu_input')) {
                    $table->time('waktu_input')->nullable()->after('tanggal');
                }
                if (!Schema::hasColumn('absensi_kelas', 'id_guru')) {
                    $table->foreignId('id_guru')->after('id_sekolah')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_kelas');
    }
};