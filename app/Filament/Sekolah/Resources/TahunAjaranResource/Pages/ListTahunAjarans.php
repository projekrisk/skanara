<?php

namespace App\Filament\Sekolah\Resources\TahunAjaranResource\Pages;

use App\Filament\Sekolah\Resources\TahunAjaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTahunAjarans extends ListRecords
{
    protected static string $resource = TahunAjaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
