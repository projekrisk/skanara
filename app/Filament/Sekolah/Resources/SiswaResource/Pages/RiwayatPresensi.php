<?php

namespace App\Filament\Sekolah\Resources\SiswaResource\Pages;

use App\Filament\Sekolah\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class RiwayatPresensi extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = SiswaResource::class;
    protected static string $view = 'filament.sekolah.resources.siswa-resource.pages.riwayat-presensi';
    public Siswa $record;
    public function mount(Siswa $record)
    {
        $this->record = $record;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Riwayat: ' . $this->record->nama_lengkap;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Presensi::query()
                    ->where('id_siswa', $this->record->id)
                    ->whereIn('status_kehadiran', ['sakit', 'izin', 'alpa', 'terlambat']) 
                    ->orderBy('tanggal', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date('d M Y')->label('Tanggal'),
                Tables\Columns\BadgeColumn::make('status_kehadiran')->label('Status')
                    ->colors(['warning' => 'terlambat', 'danger' => fn ($state) => in_array($state, ['alpa', 'sakit', 'izin'])])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('jam_masuk')->time('H:i')->label('Jam Masuk')->placeholder('-'),
                Tables\Columns\TextColumn::make('catatan')->label('Keterangan')->limit(50),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetak_laporan')
                    ->label('Cetak Riwayat')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')->label('Dari Tanggal')->required()->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('end_date')->label('Sampai Tanggal')->required()->default(now()),
                    ])
                    ->action(function (array $data, $livewire) {
                        $url = route('cetak.laporan.siswa', [
                            'id' => $livewire->record->id,
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'jenis_laporan' => 'ketidakhadiran',
                        ]);
                        $livewire->js("window.open('{$url}', '_blank')");
                    }),
            ])
            ->heading('Daftar Ketidakhadiran & Terlambat')
            ->emptyStateHeading('Siswa Rajin! Tidak ada data ketidakhadiran.');
    }
}
