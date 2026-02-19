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
        // 0. Tabel Bawaan Laravel (Cache & Jobs - WAJIB ADA)
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // 1. Tabel Paket Langganan (SaaS)
        Schema::create('paket_langganan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket');
            $table->decimal('harga', 15, 2);
            $table->integer('durasi_hari');
            $table->integer('maksimal_siswa');
            $table->integer('maksimal_pengguna');
            $table->json('fitur')->nullable(); // List fitur yang didapat
            $table->timestamps();
        });

        // 2. Tabel Sekolah (Tenant)
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('npsn')->unique();
            $table->string('nama_sekolah');
            $table->text('alamat')->nullable();
            $table->string('logo')->nullable();
            $table->string('email_admin')->nullable();
            $table->string('nama_admin')->nullable();
            
            // Konfigurasi Presensi
            $table->json('hari_kerja')->nullable(); // Array ['Senin', 'Selasa', ...]
            $table->time('jam_mulai_masuk')->default('06:00:00');
            $table->time('jam_akhir_masuk')->default('07:00:00'); // Batas masuk
            $table->integer('toleransi_terlambat')->default(0); // Menit
            $table->time('jam_mulai_pulang')->default('14:00:00');
            $table->time('jam_akhir_pulang')->default('16:00:00');

            // Status Langganan
            $table->enum('status_langganan', ['aktif', 'habis', 'masa_percobaan'])->default('masa_percobaan');
            $table->date('langganan_berakhir_pada')->nullable();
            
            // Keamanan
            $table->string('token_api_sekolah')->unique()->nullable(); // Generate otomatis nanti
            
            $table->timestamps();
        });

        // 3. Tabel Pengguna (Global User)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->nullable()->constrained('sekolah')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('peran', ['super_admin', 'admin_sekolah', 'guru', 'operator'])->default('guru');
            $table->string('foto_profil')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 4. Tabel Password Reset & Sessions (Bawaan Laravel - Wajib Ada)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 5. Tabel Rekening Bank (SaaS)
        Schema::create('rekening_bank', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('nomor_rekening');
            $table->string('atas_nama');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 6. Tabel Transaksi (SaaS)
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
            $table->foreignId('id_paket')->constrained('paket_langganan')->onDelete('cascade');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('bukti_bayar')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->timestamps();
        });

        // 7. Tabel Tahun Ajaran
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
            $table->string('nama'); // Contoh: 2024/2025
            $table->enum('semester', ['ganjil', 'genap']);
            $table->boolean('status_aktif')->default(false);
            $table->timestamps();
        });

        // 8. Tabel Kelas
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
            $table->string('nama_kelas'); // X IPA 1
            $table->integer('tingkat'); // 10, 11, 12
            $table->foreignId('id_wali_kelas')->nullable()->constrained('users')->onDelete('set null'); // User Guru
            $table->timestamps();
        });

        // 9. Tabel Siswa
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
            $table->foreignId('id_kelas')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('nis')->nullable();
            $table->string('nisn')->nullable();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('foto')->nullable();
            
            // Keamanan QR
            $table->string('kode_qr_hash')->unique(); // Hash unik
            
            $table->string('nomor_hp_ortu')->nullable();
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'keluar'])->default('aktif');
            $table->timestamps();
        });

        // 10. Tabel Perangkat (Kiosk)
        Schema::create('perangkat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
            $table->string('nama_perangkat');
            $table->string('uid_perangkat')->unique(); // ID Hardware Android
            $table->enum('status', ['diizinkan', 'diblokir'])->default('diizinkan');
            $table->timestamps();
        });

        // 11. Tabel Pegawai (Opsional - Simplified)
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
            $table->string('nip')->nullable();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('kode_qr_hash')->unique()->nullable();
            $table->timestamps();
        });

        // 12. Tabel Presensi (Data Harian)
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
            $table->foreignId('id_siswa')->nullable()->constrained('siswa')->onDelete('cascade');
            // Jika ingin mencatat presensi pegawai juga, bisa tambahkan nullable foreignId('id_pegawai')
            
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->enum('status_kehadiran', ['hadir', 'terlambat', 'izin', 'sakit', 'alpa']);
            $table->enum('metode', ['scan_kiosk', 'input_manual'])->default('scan_kiosk');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Index biar query cepat saat rekap
            $table->index(['id_sekolah', 'tanggal']);
            $table->index(['id_siswa', 'tanggal']);
        });

        // 13. Tabel Izin
        Schema::create('izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sekolah')->constrained('sekolah')->onDelete('cascade');
            $table->foreignId('id_siswa')->constrained('siswa')->onDelete('cascade');
            $table->enum('jenis', ['sakit', 'izin']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('lampiran')->nullable();
            $table->enum('status_persetujuan', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order to avoid constraint errors
        Schema::dropIfExists('izin');
        Schema::dropIfExists('presensi');
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('perangkat');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('tahun_ajaran');
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('rekening_bank');
        
        // Hapus tabel bawaan juga
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sekolah');
        Schema::dropIfExists('paket_langganan');
    }
};