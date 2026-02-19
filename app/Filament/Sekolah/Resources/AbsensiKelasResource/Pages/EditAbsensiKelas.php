<?php

namespace App\Filament\Sekolah\Resources\AbsensiKelasResource\Pages;

use App\Filament\Sekolah\Resources\AbsensiKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsensiKelas extends EditRecord
{
    protected static string $resource = AbsensiKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
