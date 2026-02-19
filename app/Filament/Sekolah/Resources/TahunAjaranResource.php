<?php

namespace App\Filament\Sekolah\Resources;

use App\Filament\Sekolah\Resources\TahunAjaranResource\Pages;
use App\Models\TahunAjaran;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;

class TahunAjaranResource extends Resource
{
    protected static ?string $model = TahunAjaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Tahun Ajaran';
    protected static ?string $modelLabel = 'Tahun Ajaran';
    protected static ?string $slug = 'tahun-ajaran';
    
    // Konfigurasi Relasi Tenant (Wajib ada agar tidak error relationship not found)
    protected static ?string $tenantRelationshipName = 'tahunAjaran';
    
    // Urutan menu di sidebar
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        // Guru TIDAK BOLEH lihat menu ini
        return auth()->user()->peran !== 'guru';
    }
    public static function canViewAny(): bool
    {
        // Guru tidak boleh akses
        return auth()->user()->peran !== 'guru';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    // ID Sekolah disembunyikan & diisi otomatis sesuai tenant yang login
                    Hidden::make('id_sekolah')
                        ->default(fn () => Filament::getTenant()->id),

                    TextInput::make('nama')
                        ->label('Tahun Ajaran')
                        ->placeholder('Contoh: 2024/2025')
                        ->required()
                        ->maxLength(20),
                    
                    Select::make('semester')
                        ->options([
                            'ganjil' => 'Ganjil',
                            'genap' => 'Genap',
                        ])
                        ->required(),
                    
                    // Status Aktif (Dropdown)
                    Select::make('status_aktif')
                        ->label('Status')
                        ->options([
                            1 => 'Aktif',
                            0 => 'Tidak Aktif',
                        ])
                        ->default(0)
                        ->required()
                        ->helperText('Jika diaktifkan, tahun ajaran lain akan otomatis non-aktif.'),
                        
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->label('Tahun')->sortable()->searchable(),
                TextColumn::make('semester')->formatStateUsing(fn (string $state): string => ucfirst($state))->sortable(),
                IconColumn::make('status_aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTahunAjarans::route('/'),
            'create' => Pages\CreateTahunAjaran::route('/create'),
            'edit' => Pages\EditTahunAjaran::route('/{record}/edit'),
        ];
    }
}
