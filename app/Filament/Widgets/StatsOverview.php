<?php

namespace App\Filament\Widgets;

use App\Models\AbsensiHarian;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Sekolah;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return Auth::check();
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        if ($user->sekolah_id) {
            $sekolahId = $user->sekolah_id;

            $totalSiswa = Siswa::where('sekolah_id', $sekolahId)->where('status_aktif', true)->count();
            $totalGuru = User::where('sekolah_id', $sekolahId)->where('peran', 'guru')->count();
            
            $hadirHariIni = AbsensiHarian::where('sekolah_id', $sekolahId)
                ->whereDate('tanggal', now())
                ->where('status', 'Hadir') 
                ->count();
            
            $persentase = $totalSiswa > 0 ? round(($hadirHariIni / $totalSiswa) * 100) : 0;
            $colorHadir = $persentase > 80 ? 'success' : 'warning';

            return [
                Stat::make('Total Siswa', $totalSiswa)
                    ->description('Siswa Aktif Terdaftar')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('primary')
                    ->chart([7, 3, 4, 5, 6, 3, $totalSiswa]),

                Stat::make('Total Guru', $totalGuru)
                    ->description('Tenaga Pengajar')
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->color('info'),

                Stat::make('Hadir Hari Ini', $hadirHariIni)
                    ->description("{$persentase}% dari total siswa")
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color($colorHadir)
                    ->chart([10, 10, 10, 10, $hadirHariIni]),
            ];
        }

        return [
            Stat::make('Total Sekolah', Sekolah::count())
                ->description('Sekolah Terdaftar')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
            
            Stat::make('Total User', User::count())
                ->description('Semua Pengguna System')
                ->color('success'),

            Stat::make('Total Siswa', Siswa::count())
                ->description('Total Siswa Terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}