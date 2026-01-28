<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\AbsensiHarian; // UPDATE: Gunakan AbsensiHarian
use App\Models\Siswa;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;

class RiwayatSiswa extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = SiswaResource::class;
    protected static string $view = 'filament.resources.siswa-resource.pages.riwayat-siswa';
    protected static ?string $title = 'Riwayat Ketidakhadiran';

    public Siswa $record;

    public function mount(Siswa $record)
    {
        $this->record = $record;
    }

    // --- TABEL RIWAYAT (Sumber: Absensi Harian) ---
    public function table(Table $table): Table
    {
        return $table
            ->query(
                AbsensiHarian::query()
                    ->where('siswa_id', $this->record->id)
                    // Menampilkan status selain Hadir (Sakit, Izin, Alpha, Telat)
                    ->whereIn('status', ['Sakit', 'Izin', 'Alpha', 'Telat']) 
                    ->orderBy('tanggal', 'desc')
            )
            ->columns([
                TextColumn::make('tanggal')
                    ->date('d F Y')
                    ->label('Tanggal')
                    ->sortable(),
                
                TextColumn::make('jam_masuk')
                    ->label('Jam Scan')
                    ->placeholder('-'), // Kosong jika S/I/A tanpa scan
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sakit' => 'info',
                        'Izin' => 'warning',
                        'Alpha' => 'danger',
                        'Telat' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Filter::make('rentang_tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->where('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->where('tanggal', '<=', $date),
                            );
                    })
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('cetak_laporan')
                    ->label('Cetak Laporan (PDF)')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn () => route('cetak.riwayat.siswa', [
                        'siswa_id' => $this->record->id,
                    ]))
                    ->openUrlInNewTab()
            ]);
    }
}