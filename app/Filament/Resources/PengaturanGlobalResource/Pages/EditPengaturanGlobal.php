<?php

namespace App\Filament\Resources\PengaturanGlobalResource\Pages;

use App\Filament\Resources\PengaturanGlobalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanGlobal extends EditRecord
{
    protected static string $resource = PengaturanGlobalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
