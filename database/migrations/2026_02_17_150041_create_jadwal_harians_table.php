<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->cascadeOnDelete();
            
            $table->string('hari'); // Senin, Selasa, dst.
            $table->time('jam_masuk'); // Jam mulai KBM
            $table->integer('toleransi_telat')->default(0); // Menit
            $table->time('jam_tutup_masuk'); // Batas akhir scan masuk (Absen ditutup)
            $table->time('jam_pulang'); // Jam mulai scan pulang
            
            $table->boolean('is_libur')->default(false); // Opsional jika ingin menandai libur di jadwal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_harian');
    }
};