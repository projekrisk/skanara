<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaketLanggananResource\Pages;
use App\Models\PaketLangganan;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaketLanggananResource extends Resource
{
    protected static ?string $model = PaketLangganan::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $modelLabel = 'Paket';
    protected static ?string $slug = 'paket';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $navigationLabel = 'Paket Langganan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Paket Upgrade')->schema([
                    TextInput::make('nama_paket')
                        ->label('Nama Paket')
                        ->placeholder('Misal: Paket Premium Tahunan')
                        ->required(),
                    TextInput::make('harga')
                        ->label('Harga Langganan')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                    TextInput::make('durasi_hari')
                        ->label('Durasi Aktif (Hari)')
                        ->numeric()
                        ->suffix('Hari')
                        ->default(365)
                        ->required(),

                    Hidden::make('maksimal_siswa')->default(10000),
                    Hidden::make('maksimal_pengguna')->default(100),
                    
                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_paket')->searchable()->weight('bold'),
                TextColumn::make('harga')->money('IDR'),
                TextColumn::make('durasi_hari')->suffix(' Hari'),
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
            'index' => Pages\ListPaketLangganans::route('/'),
            'create' => Pages\CreatePaketLangganan::route('/create'),
            'edit' => Pages\EditPaketLangganan::route('/{record}/edit'),
        ];
    }
}
