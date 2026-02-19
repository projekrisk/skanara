<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RekeningBankResource\Pages;
use App\Models\RekeningBank;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class RekeningBankResource extends Resource
{
    protected static ?string $model = RekeningBank::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $slug = 'rekening';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $navigationLabel = 'Rekening Bank';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Rekening')->schema([
                    TextInput::make('nama_bank')
                        ->label('Nama Bank')
                        ->placeholder('Misal: Bank BRI')
                        ->required(),
                    TextInput::make('nomor_rekening')
                        ->label('Nomor Rekening')
                        ->required(),
                    TextInput::make('atas_nama')
                        ->label('Atas Nama')
                        ->columnSpanFull()
                        ->required(),
                    Toggle::make('status_aktif')
                        ->label('Status Aktif')
                        ->onIcon('heroicon-m-check')
                        ->offIcon('heroicon-m-x-mark')
                        ->default(true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_bank')->searchable(),
                TextColumn::make('nomor_rekening')->copyable(),
                TextColumn::make('atas_nama'),
                ToggleColumn::make('status_aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekeningBanks::route('/'),
            'create' => Pages\CreateRekeningBank::route('/create'),
            'edit' => Pages\EditRekeningBank::route('/{record}/edit'),
        ];
    }
}
