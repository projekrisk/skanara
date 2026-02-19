<?php

namespace App\Filament\Sekolah\Resources\HariLiburResource\Pages;

use App\Filament\Sekolah\Resources\HariLiburResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHariLibur extends EditRecord
{
    protected static string $resource = HariLiburResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
