<?php

namespace App\Filament\Sekolah\Resources;

use App\Filament\Sekolah\Resources\PerangkatResource\Pages;
use App\Models\Perangkat;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PerangkatResource extends Resource
{
    protected static ?string $model = Perangkat::class;
    protected static ?string $navigationIcon = 'heroicon-o-device-tablet';
    protected static ?string $modelLabel = 'Perangkat Kiosk';
    protected static ?string $slug = 'perangkat';
    protected static ?string $tenantRelationshipName = 'perangkat';
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Data Perangkat';
    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->peran !== 'guru';
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->peran !== 'guru';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make('Identitas Perangkat')->schema([
                    TextInput::make('nama_perangkat')
                        ->label('Nama Perangkat')
                        ->placeholder('Contoh: Kiosk Lobi Utama')
                        ->required()
                        ->maxLength(255),
                    
                    TextInput::make('uid_perangkat')
                        ->label('Device ID (UID)')
                        ->helperText('Masukkan ID unik yang tertera di menu "Tentang" pada aplikasi Android.')
                        // Bagian ->password() dan ->revealable() sudah dihapus agar terlihat teks biasa
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->rule(function ($record) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                if (blank($value)) return;

                                $hashedInput = hash('sha256', $value);

                                $query = \App\Models\Perangkat::where('uid_perangkat', $hashedInput);

                                if ($record) {
                                    $query->where('id', '!=', $record->id);
                                }

                                if ($query->exists()) {
                                    $fail('Device ID ini sudah terdaftar di perangkat lain.');
                                }
                            };
                        }),

                    Select::make('status')
                        ->options([
                            'diizinkan' => 'Aktif',
                            'diblokir' => 'Nonaktif',
                        ])
                        ->default('diizinkan')
                        ->required(),
                ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_perangkat')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('uid_perangkat')
                    ->label('Device Hash')
                    ->limit(15)
                    ->fontFamily('mono')
                    ->copyable()
                    ->tooltip('Ini adalah hasil enkripsi SHA256 dari Device ID asli.'),
                
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'diizinkan',
                        'danger' => 'diblokir',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'diizinkan' => 'Aktif',
                        'diblokir' => 'Nonaktif',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerangkats::route('/'),
            'create' => Pages\CreatePerangkat::route('/create'),
            'edit' => Pages\EditPerangkat::route('/{record}/edit'),
        ];
    }
}