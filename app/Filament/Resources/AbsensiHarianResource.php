<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiHarianResource\Pages;
use App\Models\AbsensiHarian;
use App\Models\Kelas; // Import Model Kelas
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action; // Import Action

class AbsensiHarianResource extends Resource
{
    protected static ?string $model = AbsensiHarian::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Presensi Kiosk';
    protected static ?string $slug = 'presensi-harian';
    protected static ?string $navigationGroup = 'Absensi';
    protected static ?int $navigationSort = 3;

    // --- IZIN AKSES: Super Admin, Admin Sekolah, DAN Operator ---
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->sekolah_id === null) return true;
        // Operator BOLEH akses ini
        return in_array($user->peran, ['admin_sekolah', 'operator']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('siswa_id')
                    ->relationship('siswa', 'nama_lengkap')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_lengkap} ({$record->nisn}) - {$record->kelas->nama_kelas}")
                    ->searchable(['nama_lengkap', 'nisn'])
                    ->required(),
                DatePicker::make('tanggal')->required(),
                TimePicker::make('jam_masuk'),
                TimePicker::make('jam_pulang'),
                Select::make('status')
                    ->options([
                        'Hadir' => 'Hadir', 'Telat' => 'Telat', 'Izin' => 'Izin', 'Sakit' => 'Sakit', 'Alpha' => 'Alpha',
                    ]),
                TextInput::make('keterangan'),
                Select::make('sekolah_id')
                    ->relationship('sekolah', 'nama_sekolah')
                    ->required()
                    ->hidden(fn () => auth()->check() && auth()->user()->sekolah_id !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')->date('d M Y')->sortable(),
                TextColumn::make('siswa.nama_lengkap')->searchable()->weight('bold')->label('Nama Siswa'),
                TextColumn::make('siswa.kelas.nama_kelas')->label('Kelas')->sortable(),
                TextColumn::make('jam_masuk')->time('H:i')->placeholder('-'),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'Hadir' => 'success', 'Telat' => 'warning', 'Alpha' => 'danger', 'Sakit' => 'info', 'Izin' => 'info', default => 'gray',
                }),
                TextColumn::make('sumber')->label('Via'),
                TextColumn::make('sekolah.nama_sekolah')
                    ->label('Sekolah')->sortable()
                    ->hidden(fn () => auth()->check() && auth()->user()->sekolah_id !== null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('hari_ini')
                    ->query(fn (Builder $query): Builder => $query->whereDate('tanggal', now()))
                    ->label('Hari Ini')
                    ->default(),
                SelectFilter::make('status')
                    ->options(['Hadir' => 'Hadir', 'Telat' => 'Telat', 'Sakit' => 'Sakit', 'Izin' => 'Izin', 'Alpha' => 'Alpha']),
            ])
            // --- HEADER ACTIONS: TOMBOL DOWNLOAD LAPORAN ---
            ->headerActions([
                Action::make('download_laporan')
                    ->label('Download Laporan (Excel)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth())
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->default(now())
                            ->required(),
                        Select::make('kelas_id')
                            ->label('Filter Kelas (Opsional)')
                            ->options(function () {
                                // Ambil daftar kelas milik sekolah yang login
                                $sekolahId = auth()->user()->sekolah_id;
                                if ($sekolahId) {
                                    return Kelas::where('sekolah_id', $sekolahId)->pluck('nama_kelas', 'id')->toArray();
                                }
                                return Kelas::pluck('nama_kelas', 'id')->toArray();
                            })
                            ->placeholder('Semua Kelas')
                            ->searchable(),
                    ])
                    ->action(function (array $data) {
                        // Redirect ke Controller untuk download
                        return redirect()->route('download.laporan.absensi', [
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'kelas_id' => $data['kelas_id'] ?? 'all',
                        ]);
                    })
            ])
            // -----------------------------------------------
            ->actions([ Tables\Actions\EditAction::make() ])
            ->bulkActions([]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensiHarians::route('/'),
            'create' => Pages\CreateAbsensiHarian::route('/create'),
            'edit' => Pages\EditAbsensiHarian::route('/{record}/edit'),
        ];
    }
}