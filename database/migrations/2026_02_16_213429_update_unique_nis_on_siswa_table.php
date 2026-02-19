<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // 1. Cek apakah index 'siswa_nis_unique' benar-benar ada di database
            // Ini mencegah error "Can't DROP INDEX" jika index sudah hilang
            $indexName = 'siswa_nis_unique';
            $hasIndex = collect(DB::select("SHOW INDEXES FROM siswa WHERE Key_name = ?", [$indexName]))->isNotEmpty();

            if ($hasIndex) {
                $table->dropUnique($indexName);
            }

            // 2. Tambahkan unique composite baru (Scoped Unique: id_sekolah + nis)
            // Kita beri nama eksplisit agar mudah dikelola: 'siswa_sekolah_nis_unique'
            $newIndexName = 'siswa_id_sekolah_nis_unique';
            $hasNewIndex = collect(DB::select("SHOW INDEXES FROM siswa WHERE Key_name = ?", [$newIndexName]))->isNotEmpty();

            if (!$hasNewIndex) {
                $table->unique(['id_sekolah', 'nis'], $newIndexName);
            }
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // Rollback: Hapus composite, kembalikan ke global unique
            $table->dropUnique('siswa_id_sekolah_nis_unique');
            $table->unique('nis'); 
        });
    }
};