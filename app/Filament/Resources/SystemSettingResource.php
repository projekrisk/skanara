<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemSettingResource\Pages;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Sistem';
    protected static ?int $navigationSort = 99; // Paling bawah

    // Hanya Super Admin
    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->sekolah_id === null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->disabled() // Label tidak boleh diganti sembarangan
                    ->required(),
                
                // Render input sesuai tipe data di DB
                Forms\Components\Group::make()
                    ->schema(function (SystemSetting $record = null) {
                        if (!$record) return [];
                        
                        if ($record->type === 'textarea') {
                            return [Forms\Components\Textarea::make('value')->label('Isi Konten')->rows(3)];
                        }
                        return [Forms\Components\TextInput::make('value')->label('Isi Konten')];
                    })
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('value')->limit(50),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->paginated(false); // Matikan pagination karena setting sedikit
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemSettings::route('/'),
            'create' => Pages\CreateSystemSetting::route('/create'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
