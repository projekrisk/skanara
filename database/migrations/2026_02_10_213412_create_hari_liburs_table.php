<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hari_libur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('keterangan'); // Misal: Idul Fitri, HUT RI
            $table->timestamps();

            // Mencegah duplikasi tanggal di sekolah yang sama
            $table->unique(['id_sekolah', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hari_libur');
    }
};
