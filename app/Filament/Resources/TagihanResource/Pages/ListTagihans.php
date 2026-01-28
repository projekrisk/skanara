<?php

namespace App\Filament\Resources\TagihanResource\Pages;

use App\Filament\Resources\TagihanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
// Import Widget
use App\Filament\Resources\TagihanResource\Widgets\TagihanOverview;

class ListTagihans extends ListRecords
{
    protected static string $resource = TagihanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // 1. Registrasi Widget Statistik di Header Halaman
    protected function getHeaderWidgets(): array
    {
        return [
            TagihanOverview::class,
        ];
    }

    // 2. Tambahkan Tab Filter Status
    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Transaksi'),
            
            'pending' => Tab::make('Menunggu Bayar')
                ->icon('heroicon-m-clock')
                ->badge(fn () => $this->getModel()::where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            
            'sukses' => Tab::make('Berhasil (Paid)')
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid')),
            
            'gagal' => Tab::make('Gagal / Batal')
                ->icon('heroicon-m-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['rejected', 'cancelled'])),
        ];
    }
}