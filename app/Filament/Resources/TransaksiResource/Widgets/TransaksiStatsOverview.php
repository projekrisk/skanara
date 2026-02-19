<?php

namespace App\Filament\Resources\TransaksiResource\Widgets;

use App\Models\Sekolah;
use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransaksiStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Hitung Total Omzet (Hanya yang disetujui)
        $totalOmzet = Transaksi::where('status', 'disetujui')->sum('jumlah_bayar');

        // 2. Hitung Total Sekolah (Pelanggan)
        $totalSekolah = Sekolah::count();

        // 3. Hitung Sekolah Premium (Status Aktif & Belum Expired)
        // Perbaikan: Menambahkan cek tanggal agar lebih akurat
        $sekolahPremium = Sekolah::where('status_langganan', 'aktif')
            ->whereDate('langganan_berakhir_pada', '>', now()) 
            ->count();

        // 4. Hitung Total Transaksi (Semua status)
        $totalTransaksi = Transaksi::count();
        $transaksiBaru = Transaksi::where('status', 'menunggu')->count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalOmzet, 0, ',', '.'))
                ->description('Total dari transaksi disetujui')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Dummy chart visual

            Stat::make('Total Sekolah', $totalSekolah)
                ->description($sekolahPremium . ' Sekolah Premium')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary'),

            Stat::make('Total Transaksi', $totalTransaksi)
                ->description($transaksiBaru . ' Menunggu Konfirmasi')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color($transaksiBaru > 0 ? 'warning' : 'gray'),
        ];
    }
}