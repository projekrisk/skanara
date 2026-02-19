<?php

namespace App\Filament\Sekolah\Resources;

use App\Filament\Sekolah\Resources\HariLiburResource\Pages;
use App\Models\HariLibur;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HariLiburResource extends Resource
{
    protected static ?string $model = HariLibur::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $modelLabel = 'Hari Libur';
    protected static ?string $slug = 'hari-libur';

    // FIXED: Wajib didefinisikan karena nama relasi di Model Sekolah adalah 'hariLibur' (singular)
    // sedangkan Filament secara default mencari 'hariLiburs' (plural).
    protected static ?string $tenantRelationshipName = 'hariLibur';
    
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Data Hari Libur';
    protected static ?int $navigationSort = 4;

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
                    DatePicker::make('tanggal')
                        ->label('Tanggal Libur')
                        ->required()
                        ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                            return $rule->where('id_sekolah', \Filament\Facades\Filament::getTenant()->id);
                        }),
                    TextInput::make('keterangan')
                        ->label('Keterangan Libur')
                        ->placeholder('Contoh: Hari Raya Idul Fitri')
                        ->required()
                        ->maxLength(255),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('tanggal')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->searchable(),
                TextColumn::make('hari')
                    ->state(fn (HariLibur $record) => $record->tanggal->translatedFormat('l')),
            ])
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
            'index' => Pages\ListHariLiburs::route('/'),
            'create' => Pages\CreateHariLibur::route('/create'),
            'edit' => Pages\EditHariLibur::route('/{record}/edit'),
        ];
    }
}
