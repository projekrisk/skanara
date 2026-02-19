<?php

namespace App\Filament\Resources\PaketLanggananResource\Pages;

use App\Filament\Resources\PaketLanggananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaketLangganans extends ListRecords
{
    protected static string $resource = PaketLanggananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
