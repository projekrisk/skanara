<?php

namespace App\Filament\Sekolah\Widgets;

use App\Models\AbsensiKelas;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Mengatur agar widget ini di-refresh setiap beberapa detik (opsional, misal 15s)
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $user = auth()->user();
        
        // FIX: Cek apakah tenant ada, jika tidak ambil dari user
        $tenant = Filament::getTenant();
        $sekolahId = $tenant ? $tenant->id : ($user->id_sekolah ?? null);

        // Jika masih null (misal Super Admin tanpa sekolah), return kosong agar tidak error
        if (!$sekolahId) {
            return [
                Stat::make('Info', 'Data Sekolah Tidak Tersedia')
                    ->description('Pastikan akun terhubung dengan sekolah.')
                    ->color('gray'),
            ];
        }

        $today = now()->toDateString();

        // --- STATISTIK UNTUK GURU ---
        if ($user->peran === 'guru') {
            // Hitung berapa kelas yang sudah diabsen oleh guru ini hari ini
            $kelasDiabsen = AbsensiKelas::where('id_sekolah', $sekolahId)
                ->where('id_guru', $user->id)
                ->where('tanggal', $today)
                ->count();

            // Hitung total siswa yang ditandai sakit/izin/alpa oleh guru ini hari ini
            $siswaTidakHadir = AbsensiKelas::where('id_sekolah', $sekolahId)
                ->where('id_guru', $user->id)
                ->where('tanggal', $today)
                ->sum('jumlah_sakit') + 
                AbsensiKelas::where('id_sekolah', $sekolahId)
                ->where('id_guru', $user->id)
                ->where('tanggal', $today)
                ->sum('jumlah_izin') +
                AbsensiKelas::where('id_sekolah', $sekolahId)
                ->where('id_guru', $user->id)
                ->where('tanggal', $today)
                ->sum('jumlah_alpa');

            return [
                Stat::make('Input Absensi Hari Ini', $kelasDiabsen . ' Kelas')
                    ->description('Kelas yang Anda ajar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success'),

                Stat::make('Siswa Tidak Hadir', $siswaTidakHadir . ' Siswa')
                    ->description('Di kelas Anda hari ini')
                    ->icon('heroicon-o-user-minus')
                    ->color('warning'),
            ];
        }

        // --- STATISTIK UNTUK ADMIN SEKOLAH (DEFAULT) ---
        
        $hadir = Presensi::where('id_sekolah', $sekolahId)->where('tanggal', $today)->where('status_kehadiran', 'hadir')->count();
        $telat = Presensi::where('id_sekolah', $sekolahId)->where('tanggal', $today)->where('status_kehadiran', 'terlambat')->count();
        $alpha = Presensi::where('id_sekolah', $sekolahId)->where('tanggal', $today)->where('status_kehadiran', 'alpa')->count();
        $totalSiswa = Siswa::where('id_sekolah', $sekolahId)->where('status', 'aktif')->count();

        // Hitung persentase kehadiran
        $persentase = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100) : 0;

        return [
            Stat::make('Total Siswa', $totalSiswa)
                ->description('Siswa Aktif')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Kehadiran Hari Ini', $hadir)
                ->description("{$persentase}% dari total siswa")
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Dummy chart visual

            Stat::make('Terlambat', $telat)
                ->icon('heroicon-o-clock')
                ->color('warning'),
                
            Stat::make('Tanpa Keterangan (Alpa)', $alpha)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}