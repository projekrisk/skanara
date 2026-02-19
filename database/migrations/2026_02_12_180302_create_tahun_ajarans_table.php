<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu apakah tabel sudah ada untuk mencegah error "Table already exists"
        if (!Schema::hasTable('tahun_ajaran')) {
            Schema::create('tahun_ajaran', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
                $table->string('nama'); // Contoh: "2024/2025"
                $table->enum('semester', ['ganjil', 'genap']);
                $table->boolean('status_aktif')->default(false);
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
