<?php

namespace App\Filament\Resources\PengaturanGlobalResource\Pages;

use App\Filament\Resources\PengaturanGlobalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengaturanGlobals extends ListRecords
{
    protected static string $resource = PengaturanGlobalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
