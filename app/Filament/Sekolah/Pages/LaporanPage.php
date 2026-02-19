<?php

namespace App\Filament\Sekolah\Pages;

use App\Models\Kelas;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Facades\Filament;

class LaporanPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $title = 'Laporan Presensi';
    protected static ?string $slug = 'laporan';
    protected static string $view = 'filament.sekolah.pages.laporan-page';
    
    protected static ?string $navigationGroup = 'Kehadiran';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (in_array($user->peran, ['admin_sekolah', 'operator'])) {
            return true;
        }

        if ($user->peran === 'guru') {
            // FIX: Ambil ID Sekolah dengan aman
            $tenant = Filament::getTenant();
            $sekolahId = $tenant ? $tenant->id : $user->id_sekolah;

            return Kelas::where('id_wali_kelas', $user->id)
                ->where('id_sekolah', $sekolahId)
                ->exists();
        }

        return false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $defaultKelas = null;
        
        // FIX: Ambil ID Sekolah dengan aman
        $tenant = Filament::getTenant();
        $sekolahId = $tenant ? $tenant->id : $user->id_sekolah;

        if ($user->peran === 'guru') {
            $kelasWali = Kelas::where('id_wali_kelas', $user->id)
                ->where('id_sekolah', $sekolahId)
                ->first();
            
            if ($kelasWali) {
                $defaultKelas = $kelasWali->id;
            }
        }

        $this->form->fill([
            'jenis_laporan' => 'harian',
            'tanggal' => now()->format('Y-m-d'),
            'bulan' => now()->format('m'),
            'tahun' => now()->format('Y'),
            'id_kelas' => $defaultKelas,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Laporan')->schema([
                    Select::make('jenis_laporan')
                        ->label('Jenis Laporan')
                        ->options([
                            'harian' => 'Laporan Harian (Detail Jam)',
                            'bulanan' => 'Laporan Bulanan (Rekap H, S, I, A)',
                        ])
                        ->live() // Aktifkan live update untuk menyembunyikan input lain
                        ->required(),

                    DatePicker::make('tanggal')
                        ->label('Pilih Tanggal')
                        ->required()
                        // Hanya tampil jika pilihannya harian
                        ->visible(fn (\Filament\Forms\Get $get) => $get('jenis_laporan') === 'harian'),

                    Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options([
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                        ])
                        ->required()
                        ->visible(fn (\Filament\Forms\Get $get) => $get('jenis_laporan') === 'bulanan'),

                    TextInput::make('tahun')
                        ->label('Tahun')
                        ->numeric() // Hanya angka
                        ->default(date('Y')) // Default tahun saat ini
                        ->required()
                        ->visible(fn (\Filament\Forms\Get $get) => $get('jenis_laporan') === 'bulanan'),

                    Select::make('id_kelas')
                        ->label('Filter Kelas')
                        ->options(function () {
                            $user = auth()->user();
                            
                            // FIX: Ambil ID Sekolah dengan aman agar tidak error "property id on null"
                            $tenant = Filament::getTenant();
                            $sekolahId = $tenant ? $tenant->id : $user->id_sekolah;

                            $query = Kelas::where('id_sekolah', $sekolahId);

                            if ($user->peran === 'guru') {
                                $query->where('id_wali_kelas', $user->id);
                            }
                            
                            return $query->pluck('nama_kelas', 'id');
                        })
                        ->searchable()
                        // Placeholder dinamis
                        ->placeholder(fn () => 
                            auth()->user()->peran === 'guru'
                            ? 'Kelas Binaan Anda' 
                            : 'Semua Kelas (Kosongkan jika semua)'
                        )
                        ->required(fn () => auth()->user()->peran === 'guru'),
                ])->columns(2),
            ])
            ->statePath('data');
    }

    public function cetakLaporan()
    {
        $data = $this->form->getState();

        $url = route('laporan.presensi.cetak', [
            'jenis' => $data['jenis_laporan'],
            'tanggal' => $data['tanggal'] ?? null,
            'bulan' => $data['bulan'] ?? null,
            'tahun' => $data['tahun'] ?? null,
            'id_kelas' => $data['id_kelas'] ?? null,
        ]);

        $this->js("window.open('{$url}', '_blank');");
    }
}