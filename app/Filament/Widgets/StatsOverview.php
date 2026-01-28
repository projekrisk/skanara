<?php

namespace App\Filament\Widgets;

use App\Models\AbsensiHarian;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    // Mengatur urutan tampilan (paling atas di dashboard)
    protected static ?int $sort = 1;

    // Filter Akses: Pastikan hanya tampil jika user sudah login
    public static function canView(): bool
    {
        return Auth::check();
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        // ----------------------------------------
        // LOGIKA 1: JIKA YANG LOGIN ADMIN SEKOLAH
        // ----------------------------------------
        if ($user->sekolah_id) {
            $sekolahId = $user->sekolah_id;

            // Hitung data spesifik sekolah ini
            $totalSiswa = Siswa::where('sekolah_id', $sekolahId)->where('status_aktif', true)->count();
            $totalGuru = User::where('sekolah_id', $sekolahId)->where('peran', 'guru')->count();
            
            // Hitung kehadiran hari ini (dari Absensi Kiosk)
            $hadirHariIni = AbsensiHarian::where('sekolah_id', $sekolahId)
                ->whereDate('tanggal', now())
                ->where('status', 'Hadir') 
                ->count();
            
            // Hitung persentase kehadiran (untuk dekorasi visual)
            $persentase = $totalSiswa > 0 ? round(($hadirHariIni / $totalSiswa) * 100) : 0;
            $colorHadir = $persentase > 80 ? 'success' : 'warning'; // Hijau jika > 80%, Kuning jika kurang

            return [
                Stat::make('Total Siswa', $totalSiswa)
                    ->description('Siswa Aktif Terdaftar')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('primary')
                    ->chart([7, 3, 4, 5, 6, 3, $totalSiswa]), // Chart dekoratif

                Stat::make('Total Guru', $totalGuru)
                    ->description('Tenaga Pengajar')
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->color('info'),

                Stat::make('Hadir Hari Ini', $hadirHariIni)
                    ->description("{$persentase}% dari total siswa")
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color($colorHadir)
                    ->chart([10, 10, 10, 10, $hadirHariIni]), // Chart dekoratif
            ];
        }

        // ----------------------------------------
        // LOGIKA 2: JIKA YANG LOGIN SUPER ADMIN
        // ----------------------------------------
        // Super admin melihat rekap global seluruh sistem SaaS
        return [
            Stat::make('Total Sekolah', \App\Models\Sekolah::count())
                ->description('Sekolah Terdaftar')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
            
            Stat::make('Total User', User::count())
                ->description('Semua Pengguna System')
                ->color('success'),

            Stat::make('Transaksi Sukses', \App\Models\Tagihan::where('status', 'paid')->count())
                ->description('Tagihan Lunas')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}
