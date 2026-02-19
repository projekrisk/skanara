<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sekolah', 'hari_kerja')) {
            Schema::table('sekolah', function (Blueprint $table) {
                // Default 5 hari kerja (Senin - Jumat)
                $table->unsignedTinyInteger('hari_kerja')->default(5)->after('nama_sekolah');
            });
        }
    }

    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn('hari_kerja');
        });
    }
};
