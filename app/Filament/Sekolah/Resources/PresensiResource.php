<?php

namespace App\Filament\Sekolah\Resources;

use App\Filament\Sekolah\Resources\PresensiResource\Pages;
use App\Models\Presensi;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $modelLabel = 'Presensi Kiosk';
    protected static ?string $slug = 'presensi';
    
    protected static ?string $navigationGroup = 'Kehadiran';
    protected static ?string $navigationLabel = 'Presensi Kiosk';
    protected static ?int $navigationSort = 1;

    // Pastikan tenancy diaktifkan
    protected static ?string $tenantRelationshipName = 'presensi';

    public static function shouldRegisterNavigation(): bool
    {
        // Guru tidak perlu melihat menu ini, karena mereka punya menu Absensi Kelas sendiri
        return auth()->user()->peran !== 'guru';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_siswa')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama_lengkap', fn (Builder $query) => 
                        $query->where('id_sekolah', Filament::getTenant()->id)->where('status', 'aktif')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('tanggal')
                    ->default(now())
                    ->required(),

                TimePicker::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->seconds(false)
                    ->default(now()),

                TimePicker::make('jam_keluar')
                    ->label('Jam Pulang')
                    ->seconds(false),

                Select::make('status_kehadiran')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'alpa' => 'Alpa',
                        'terlambat' => 'Terlambat',
                        'pulang' => 'Pulang',
                    ])
                    ->default('hadir')
                    ->required(),
                
                TextInput::make('metode')
                    ->label('Metode')
                    ->default('manual')
                    ->readOnly(),

                Textarea::make('catatan')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // --- FILTER BAWAAN: HANYA HARI INI ---
            ->modifyQueryUsing(function (Builder $query) {
                // Tampilkan data hari ini saja secara default agar ringan
                return $query->whereDate('tanggal', now()->toDateString());
            })
            // -------------------------------------
            
            // Urutkan berdasarkan update terakhir (data baru masuk/pulang)
            ->defaultSort('updated_at', 'desc') 
            
            ->columns([
                TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable(),
                
                TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('siswa.kelas.nama_kelas')
                    ->label('Kelas')
                    ->sortable(),

                TextColumn::make('jam_masuk')
                    ->label('Masuk')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('jam_keluar')
                    ->label('Pulang')
                    ->time('H:i')
                    ->placeholder('-'),

                BadgeColumn::make('status_kehadiran')
                    ->label('Status')
                    ->colors([
                        'success' => 'hadir',
                        'warning' => 'sakit',
                        'primary' => 'izin',
                        'danger' => 'alpa',
                        'gray' => 'pulang',
                        'danger' => 'terlambat',
                    ]),

                TextColumn::make('metode')
                    ->label('Via')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                // Filter tanggal (range) dihapus karena tampilan sudah dilock ke hari ini.
                // Admin hanya butuh filter sederhana untuk data hari ini.

                SelectFilter::make('status_kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'alpa' => 'Alpa',
                        'terlambat' => 'Terlambat',
                    ]),
                
                SelectFilter::make('kelas')
                    ->relationship('siswa.kelas', 'nama_kelas')
                    ->label('Kelas'),
            ])
            ->headerActions([
                // Tombol Laporan dihapus karena sudah ada menu Laporan Terpusat
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->poll('10s'); // Auto refresh setiap 10 detik agar realtime monitoring
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPresensis::route('/'),
            'create' => Pages\CreatePresensi::route('/create'),
            'edit' => Pages\EditPresensi::route('/{record}/edit'),
        ];
    }
}