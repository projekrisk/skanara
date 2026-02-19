<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaturanGlobalResource\Pages;
use App\Models\PengaturanGlobal;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PengaturanGlobalResource extends Resource
{
    protected static ?string $model = PengaturanGlobal::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $modelLabel = 'Pengaturan';
    protected static ?string $slug = 'pengaturan';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('judul')
                        ->label('Judul Widget')
                        ->required(),
                    TextInput::make('versi')
                        ->label('Versi Aplikasi')
                        ->placeholder('1.0.0')
                        ->required(),
                    TextInput::make('link_download')
                        ->label('Link Download (URL)')
                        ->url()
                        ->placeholder('https://...')
                        ->required()
                        ->columnSpanFull(),
                    Textarea::make('deskripsi')
                        ->label('Deskripsi / Changelog')
                        ->rows(3)
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Tampilkan Widget')
                        ->default(true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul'),
                TextColumn::make('versi')->badge()->color('info'),
                TextColumn::make('link_download')->limit(30)->copyable(),
                ToggleColumn::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }
    
    public static function canCreate(): bool
    {
        return PengaturanGlobal::count() === 0;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengaturanGlobals::route('/'),
            'create' => Pages\CreatePengaturanGlobal::route('/create'),
            'edit' => Pages\EditPengaturanGlobal::route('/{record}/edit'),
        ];
    }
}
