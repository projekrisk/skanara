<?php

namespace App\Filament\Widgets;

use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuperAdminStatsOverview extends BaseWidget
{
    // Refresh setiap 30 detik
    protected static ?string $pollingInterval = '30s';

    // Pastikan widget ini hanya muncul di dashboard, bukan di halaman resource
    protected static ?int $sort = 1;

    // Logika agar hanya tampil untuk Super Admin (jika panel digabung)
    public static function canView(): bool
    {
        return auth()->user()->peran === 'super_admin';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Sekolah', Sekolah::count())
                ->description('Sekolah Terdaftar')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]), // Dummy chart visual

            Stat::make('Total Guru', User::where('peran', 'guru')->count())
                ->description('User Guru Aktif')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Total Siswa', Siswa::count()) // Menghitung semua siswa dari semua sekolah
                ->description('Data Siswa Terinput')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}