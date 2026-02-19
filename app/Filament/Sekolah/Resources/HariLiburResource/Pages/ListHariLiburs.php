<?php

namespace App\Filament\Sekolah\Resources\HariLiburResource\Pages;

use App\Filament\Sekolah\Resources\HariLiburResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHariLiburs extends ListRecords
{
    protected static string $resource = HariLiburResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
