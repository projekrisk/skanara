<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_globals', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->default('Download Aplikasi Skanara');
            $table->text('deskripsi')->nullable();
            $table->string('link_download'); // URL ke Google Drive / Playstore
            $table->string('versi')->default('1.0.0');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_globals');
    }
};
