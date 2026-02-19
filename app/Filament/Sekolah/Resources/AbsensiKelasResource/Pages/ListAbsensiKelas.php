<?php

namespace App\Filament\Sekolah\Resources\AbsensiKelasResource\Pages;

use App\Filament\Sekolah\Resources\AbsensiKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsensiKelas extends ListRecords
{
    protected static string $resource = AbsensiKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
