<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_langganan', function (Blueprint $table) {
            // Tambahkan kolom deskripsi dan fitur jika belum ada
            if (!Schema::hasColumn('paket_langganan', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('durasi_hari');
            }
            if (!Schema::hasColumn('paket_langganan', 'fitur')) {
                $table->json('fitur')->nullable()->after('deskripsi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('paket_langganan', function (Blueprint $table) {
            $table->dropColumn(['deskripsi', 'fitur']);
        });
    }
};