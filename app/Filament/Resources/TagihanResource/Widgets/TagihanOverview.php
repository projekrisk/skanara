<?php

namespace App\Filament\Resources\TagihanResource\Widgets;

use App\Models\Tagihan;
use App\Models\Sekolah;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TagihanOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Hitung Total Pendapatan (Hanya yang status 'paid')
        $totalPendapatan = Tagihan::where('status', 'paid')->sum('jumlah_bayar');

        // 2. Hitung Total Transaksi (Semua status)
        $totalTransaksi = Tagihan::count();
        $transaksiSukses = Tagihan::where('status', 'paid')->count();

        // 3. Hitung Jumlah Pelanggan Aktif (Sekolah dengan paket != free)
        $pelangganPremium = Sekolah::where('paket_langganan', '!=', 'free')
            ->where('status_aktif', true)
            ->count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.'))
                ->description('Omset dari paket berbayar')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([1000000, 2500000, 5000000, $totalPendapatan]), // Chart dummy visual

            Stat::make('Transaksi', $totalTransaksi)
                ->description("{$transaksiSukses} Berhasil Dibayar")
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),

            Stat::make('Pelanggan Premium', $pelangganPremium)
                ->description('Sekolah Berlangganan Aktif')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('warning'),
        ];
    }
}