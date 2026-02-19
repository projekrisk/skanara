<?php

namespace App\Filament\Sekolah\Resources;

use App\Filament\Sekolah\Resources\KelasResource\Pages;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Kelas';
    protected static ?string $slug = 'kelas';
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Data Kelas';
    protected static ?int $navigationSort = 2;

    // Filter Global agar hanya menampilkan data milik sekolah yang login
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $tenant = Filament::getTenant();
        
        if ($tenant) {
            $query->where('id_sekolah', $tenant->id);
        } else {
            $user = auth()->user();
            if ($user && $user->id_sekolah) {
                $query->where('id_sekolah', $user->id_sekolah);
            }
        }
        
        return $query;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Guru tidak perlu melihat menu manajemen kelas
        return auth()->user()->peran !== 'guru';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\Hidden::make('id_sekolah')
                        ->default(fn () => Filament::getTenant()?->id ?? auth()->user()->id_sekolah),

                    TextInput::make('nama_kelas')
                        ->label('Nama Kelas')
                        ->placeholder('Contoh: X-E1')
                        ->required(),
                    
                    TextInput::make('tingkat')
                        ->label('Tingkat Kelas')
                        ->numeric()
                        ->placeholder('Contoh: 10')
                        ->required(),
                        
                    Select::make('id_wali_kelas')
                        ->label('Wali Kelas (Guru)')
                        ->relationship(
                            name: 'waliKelas', 
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query
                                ->where('peran', 'guru')
                                ->where('id_sekolah', Filament::getTenant()?->id ?? auth()->user()->id_sekolah)
                        )
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih Wali Kelas (Opsional)'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // FIX: Natural Sort Order (Panjang string dulu, baru abjad)
            // Ini membuat X-E1 (len 4) muncul sebelum X-E10 (len 5)
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->orderByRaw('LENGTH(nama_kelas) ASC')
                    ->orderBy('nama_kelas', 'ASC');
            })
            ->columns([
                TextColumn::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(), // User masih bisa klik header untuk sort manual jika mau
                    
                TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->sortable(),
                    
                TextColumn::make('waliKelas.name')
                    ->label('Wali Kelas')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Belum diatur'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}