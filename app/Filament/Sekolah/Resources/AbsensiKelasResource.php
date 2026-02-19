<?php

namespace App\Filament\Sekolah\Resources;

use App\Filament\Sekolah\Resources\AbsensiKelasResource\Pages;
use App\Models\AbsensiKelas;
use App\Models\Presensi;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Siswa; // Pastikan import ini ada
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;

class AbsensiKelasResource extends Resource
{
    protected static ?string $model = AbsensiKelas::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Absensi Kelas';
    protected static ?string $modelLabel = 'Absensi Kelas';
    protected static ?string $slug = 'absensi';

    protected static ?string $navigationGroup = 'Kehadiran';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()->peran, ['super_admin', 'admin_sekolah', 'guru', 'operator']);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->peran === 'guru') {
            $query->where('id_guru', $user->id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Jurnal')->schema([
                    Hidden::make('id_sekolah')
                        ->default(fn () => Filament::getTenant()?->id ?? auth()->user()->id_sekolah),

                    DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->default(now())
                        ->required(),
                    
                    TimePicker::make('waktu_input')
                        ->label('Jam Input')
                        ->default(now())
                        ->required(),

                    Select::make('id_kelas')
                        ->label('Kelas')
                        ->options(function () {
                            $tenant = Filament::getTenant();
                            $sekolahId = $tenant ? $tenant->id : auth()->user()->id_sekolah;
                            return Kelas::where('id_sekolah', $sekolahId)->pluck('nama_kelas', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('id_guru')
                        ->label('Guru Pengampu')
                        ->options(function () {
                            $tenant = Filament::getTenant();
                            $sekolahId = $tenant ? $tenant->id : auth()->user()->id_sekolah;
                            return User::where('id_sekolah', $sekolahId)
                                ->where('peran', 'guru')
                                ->pluck('name', 'id');
                        })
                        ->default(auth()->id())
                        ->required()
                        ->searchable(),
                ])->columns(2),

                Forms\Components\Section::make('Rekapitulasi Kehadiran')
                    ->description('Masukkan jumlah siswa berdasarkan statusnya.')
                    ->schema([
                        TextInput::make('jumlah_hadir')
                            ->label('Hadir')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                            
                        TextInput::make('jumlah_sakit')
                            ->label('Sakit')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                            
                        TextInput::make('jumlah_izin')
                            ->label('Izin')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                            
                        TextInput::make('jumlah_alpa')
                            ->label('Alpa')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                    ])->columns(4),
                
                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan Tambahan (Opsional)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('tanggal', 'desc')->orderBy('waktu_input', 'desc'))
            ->columns([
                TextColumn::make('tanggal')
                    ->date('d F Y')
                    ->sortable()
                    ->label('Tanggal'),
                
                TextColumn::make('waktu_input')
                    ->time('H:i')
                    ->label('Jam'),
                
                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guru.name')
                    ->label('Guru')
                    ->searchable()
                    ->visible(fn () => auth()->user()->peran !== 'guru'),
                
                TextColumn::make('rekap_ketidakhadiran')
                    ->label('Rekap (H / S / I / A)')
                    ->state(fn (AbsensiKelas $record) => 
                        "H: {$record->jumlah_hadir} | S: {$record->jumlah_sakit} | I: {$record->jumlah_izin} | A: {$record->jumlah_alpa}"
                    )
                    ->badge()
                    ->color(fn (string $state) => str_contains($state, 'S: 0 | I: 0 | A: 0') ? 'success' : 'warning'),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('tanggal_input')->label('Pilih Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_input'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', $date),
                            );
                    }),
                
                SelectFilter::make('id_kelas')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama_kelas'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetak_laporan')
                    ->label('Cetak Laporan Bulanan')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])
                            ->default(date('m'))
                            ->required(),
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2020), range(date('Y'), 2020)))
                            ->default(date('Y'))
                            ->required(),
                        
                        Select::make('id_kelas')
                            ->label('Filter Kelas (Opsional)')
                            ->options(function () {
                                $tenant = Filament::getTenant();
                                $sekolahId = $tenant ? $tenant->id : auth()->user()->id_sekolah;
                                
                                return Kelas::where('id_sekolah', $sekolahId)
                                    ->pluck('nama_kelas', 'id');
                            })
                            ->searchable(),
                    ])
                    ->action(function (array $data) {
                        $url = route('laporan.absensi-kelas.print', [
                            'bulan' => $data['bulan'],
                            'tahun' => $data['tahun'],
                            'id_kelas' => $data['id_kelas'] ?? null,
                        ]);
                        redirect()->away($url);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->modalHeading('Detail Ketidakhadiran Siswa'),
                
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Dasar')
                    ->schema([
                        TextEntry::make('tanggal')->date('d F Y'),
                        TextEntry::make('waktu_input')->time('H:i'),
                        TextEntry::make('kelas.nama_kelas'),
                        TextEntry::make('guru.name')->label('Guru Pengampu'),
                        TextEntry::make('catatan')->label('Catatan')->columnSpanFull(),
                    ])->columns(4),

                Section::make('Daftar Siswa Tidak Hadir')
                    ->schema([
                        TextEntry::make('detail_siswa')
                            ->hiddenLabel()
                            ->html()
                            ->state(function (AbsensiKelas $record) {
                                // 1. PRIORITAS UTAMA: Cek Data JSON di tabel absensi_kelas (Data dari Android)
                                if (!empty($record->detail_kehadiran)) {
                                    $details = collect($record->detail_kehadiran);
                                    
                                    // Ambil nama siswa berdasarkan ID yang ada di JSON
                                    $studentIds = $details->pluck('id_siswa');
                                    $students = Siswa::whereIn('id', $studentIds)->pluck('nama_lengkap', 'id');
                                    
                                    $html = '<ul class="list-disc pl-5 space-y-1">';
                                    
                                    foreach ($details as $item) {
                                        $idSiswa = $item['id_siswa'] ?? null;
                                        $statusRaw = $item['status'] ?? '-';
                                        $ket = isset($item['keterangan']) && $item['keterangan'] ? "({$item['keterangan']})" : "";
                                        
                                        $nama = $students[$idSiswa] ?? "Siswa ID: $idSiswa";
                                        
                                        $statusColor = match(strtolower($statusRaw)) {
                                            'sakit' => 'text-warning-600',
                                            'izin' => 'text-primary-600',
                                            'alpa' => 'text-danger-600',
                                            default => 'text-gray-600'
                                        };
                                        $status = strtoupper($statusRaw);

                                        $html .= "<li>
                                            <span class='font-medium'>{$nama}</span> 
                                            <span class='font-bold {$statusColor}'>[$status]</span> 
                                            <span class='text-sm text-gray-500'>$ket</span>
                                        </li>";
                                    }
                                    $html .= '</ul>';
                                    return $html;
                                }

                                // 2. FALLBACK: Jika JSON kosong (Input Manual Lama), cek tabel Presensi
                                $siswaTidakHadir = Presensi::with('siswa')
                                    ->where('id_sekolah', $record->id_sekolah)
                                    ->where('tanggal', $record->tanggal)
                                    ->whereHas('siswa', fn($q) => $q->where('id_kelas', $record->id_kelas))
                                    ->whereIn('status_kehadiran', ['sakit', 'izin', 'alpa'])
                                    ->get();

                                if ($siswaTidakHadir->isEmpty()) {
                                    return '<span class="text-success-600 font-bold">Semua Siswa Hadir / Data Detail Tidak Tersedia</span>';
                                }

                                $html = '<ul class="list-disc pl-5 space-y-1">';
                                foreach ($siswaTidakHadir as $p) {
                                    $statusColor = match($p->status_kehadiran) {
                                        'sakit' => 'text-warning-600',
                                        'izin' => 'text-primary-600',
                                        'alpa' => 'text-danger-600',
                                        default => 'text-gray-600'
                                    };
                                    
                                    $ket = $p->catatan ? "({$p->catatan})" : "";
                                    $status = strtoupper($p->status_kehadiran);
                                    
                                    $html .= "<li>
                                        <span class='font-medium'>{$p->siswa->nama_lengkap}</span> 
                                        <span class='font-bold {$statusColor}'>[$status]</span> 
                                        <span class='text-sm text-gray-500'>$ket</span>
                                    </li>";
                                }
                                $html .= '</ul>';
                                return $html;
                            }),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensiKelas::route('/'),
            'create' => Pages\CreateAbsensiKelas::route('/create'),
            'edit' => Pages\EditAbsensiKelas::route('/{record}/edit'),
        ];
    }
}