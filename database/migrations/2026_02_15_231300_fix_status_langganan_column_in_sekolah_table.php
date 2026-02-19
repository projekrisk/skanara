<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah kolom status_langganan menjadi VARCHAR agar bisa menerima nilai 'free', 'pro', dll.
        if (config('database.default') === 'mysql') {
             DB::statement("ALTER TABLE sekolah MODIFY COLUMN status_langganan VARCHAR(50) DEFAULT 'free'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu dikembalikan agar data tidak rusak
    }
};