<?php

namespace App\Filament\Sekolah\Resources\PerangkatResource\Pages;

use App\Filament\Sekolah\Resources\PerangkatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerangkats extends ListRecords
{
    protected static string $resource = PerangkatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
