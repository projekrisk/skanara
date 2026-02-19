<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SekolahResource\Pages;
use App\Models\Sekolah;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SekolahResource extends Resource
{
    protected static ?string $model = Sekolah::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';    
    protected static ?string $modelLabel = 'Sekolah';    
    protected static ?string $slug = 'sekolah';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?string $navigationLabel = 'Sekolah';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    FileUpload::make('logo')
                        ->label('Logo Sekolah')
                        ->directory('sekolah-logo')
                        ->visibility('public')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'items-center justify-center']), 
                ]),

                Tabs::make('Data Sekolah')
                    ->tabs([
                        Tabs\Tab::make('Identitas')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('npsn')
                                    ->label('NPSN')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('nama_sekolah')
                                    ->label('Nama Sekolah')
                                    ->required(),
                                TextInput::make('nama_admin')->label('Nama Kontak Admin'),
                                TextInput::make('email_admin')->label('Email Admin')->email(),
                                Textarea::make('alamat')
                                    ->label('Alamat Lengkap')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Tabs\Tab::make('Pengaturan')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                TimePicker::make('jam_mulai_masuk')->label('Buka Absen Masuk')->required(),
                                TimePicker::make('jam_akhir_masuk')->label('Tutup Absen Masuk')->required(),
                                TextInput::make('toleransi_terlambat')->label('Toleransi (Menit)')->numeric()->default(0),
                                TimePicker::make('jam_mulai_pulang')->label('Buka Absen Pulang')->required(),
                                TimePicker::make('jam_akhir_pulang')->label('Tutup Absen Pulang')->required(),
                            ])->columns(3),

                        Tabs\Tab::make('Langganan')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Select::make('status_langganan')
                                    ->label('Status Langganan')
                                    ->options([
                                        'aktif' => 'Aktif',
                                        'habis' => 'Habis',
                                        'masa_percobaan' => 'Masa Percobaan',
                                        'free' => 'Free',
                                        'premium' => 'Premium',
                                    ])
                                    ->default('free')
                                    ->required(),
                                DatePicker::make('langganan_berakhir_pada')->label('Berakhir Pada'),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular(),

                TextColumn::make('nama_sekolah')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('siswa_count')
                    ->counts('siswa') 
                    ->label('Jml Siswa')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('status_langganan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'masa_percobaan' => 'warning',
                        'habis' => 'danger',
                        'free' => 'info',
                        'premium' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('langganan_berakhir_pada')
                    ->label('Berakhir Pada')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSekolahs::route('/'),
            'create' => Pages\CreateSekolah::route('/create'),
            'edit' => Pages\EditSekolah::route('/{record}/edit'),
        ];
    }
}