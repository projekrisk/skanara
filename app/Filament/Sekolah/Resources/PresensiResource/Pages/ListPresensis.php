<?php

namespace App\Filament\Sekolah\Resources\PresensiResource\Pages;

use App\Filament\Sekolah\Resources\PresensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresensis extends ListRecords
{
    protected static string $resource = PresensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    // Fungsi getTabs() dihapus untuk menghilangkan tab filter di atas tabel
}