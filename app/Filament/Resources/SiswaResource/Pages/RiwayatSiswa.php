<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\DetailJurnal;
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

    public Siswa $record; // Menyimpan data siswa yang sedang dilihat

    public function mount(Siswa $record)
    {
        $this->record = $record;
    }

    // --- TABEL RIWAYAT ---
    public function table(Table $table): Table
    {
        return $table
            ->query(
                DetailJurnal::query()
                    ->where('siswa_id', $this->record->id)
                    ->whereIn('status', ['Sakit', 'Izin', 'Alpha']) // Hanya ketidakhadiran
                    ->join('jurnal_guru', 'detail_jurnal.jurnal_guru_id', '=', 'jurnal_guru.id') // Join untuk ambil tanggal
                    ->select('detail_jurnal.*', 'jurnal_guru.tanggal', 'jurnal_guru.mata_pelajaran', 'jurnal_guru.jam_ke')
                    ->orderBy('jurnal_guru.tanggal', 'desc')
            )
            ->columns([
                TextColumn::make('tanggal')->date('d F Y')->label('Tanggal')->sortable(),
                TextColumn::make('mata_pelajaran')->label('Mapel'),
                TextColumn::make('jam_ke')->label('Jam'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sakit' => 'info',
                        'Izin' => 'warning',
                        'Alpha' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                // Filter Rentang Tanggal
                Filter::make('rentang_tanggal')
                    ->form([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->where('jurnal_guru.tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->where('jurnal_guru.tanggal', '<=', $date),
                            );
                    })
            ])
            ->headerActions([
                // Tombol Cetak Laporan
                \Filament\Tables\Actions\Action::make('cetak_laporan')
                    ->label('Cetak Laporan (PDF)')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn () => route('cetak.riwayat.siswa', [
                        'siswa_id' => $this->record->id,
                        // Kita kirim parameter filter via query string jika ada (opsional, perlu js tambahan untuk ambil value filter real-time, 
                        // tapi untuk simpel kita cetak bulan ini atau semua).
                        // Untuk tahap ini kita buat cetak semua/bulan ini di controller.
                    ]))
                    ->openUrlInNewTab()
            ]);
    }
}