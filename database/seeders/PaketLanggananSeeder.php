<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaketLangganan;

class PaketLanggananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Paket Free (3 Bulan / 90 Hari)
        PaketLangganan::updateOrCreate(
            ['nama_paket' => 'Free'],
            [
                'harga' => 0,
                'durasi_hari' => 90,
                'maksimal_siswa' => 50,
                'maksimal_pengguna' => 5, // PERBAIKAN: Menambahkan field wajib ini
                'deskripsi' => 'Paket percobaan gratis selama 3 bulan dengan fitur dasar.',
                // Gunakan json_encode agar aman jika casting di Model belum diset
                'fitur' => json_encode(['Maks 50 Siswa', 'Laporan Standar', 'Support Email']),
            ]
        );

        // Paket Premium (1 Tahun / 365 Hari)
        PaketLangganan::updateOrCreate(
            ['nama_paket' => 'Premium'],
            [
                'harga' => 1500000,
                'durasi_hari' => 365,
                'maksimal_siswa' => 10000,
                'maksimal_pengguna' => 500, // PERBAIKAN: Menambahkan field wajib ini
                'deskripsi' => 'Akses penuh ke semua fitur selama 1 tahun.',
                'fitur' => json_encode(['Unlimited Siswa', 'Laporan Lengkap', 'Prioritas Support', 'Backup Otomatis']),
            ]
        );
    }
}