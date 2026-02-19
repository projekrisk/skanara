<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Transaksi Terbaru';
    
    protected static ?int $sort = 2;
    
    // Agar tabel mengambil lebar penuh
    protected int | string | array $columnSpan = 'full';

    // Logika agar hanya tampil untuk Super Admin
    public static function canView(): bool
    {
        return auth()->user()->peran === 'super_admin';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Ambil 5 transaksi terbaru dari semua sekolah
                Transaksi::query()->with(['sekolah', 'paket'])->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sekolah.nama_sekolah')
                    ->label('Sekolah')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('paket.nama_paket')
                    ->label('Paket')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('jumlah_bayar')
                    ->label('Nominal')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'diproses' => 'info',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                
                // Menampilkan tombol aksi kecil untuk melihat detail/bukti bayar langsung
                Tables\Columns\ImageColumn::make('bukti_bayar')
                    ->label('Bukti')
                    ->circular(),
            ])
            ->actions([
                // Aksi untuk melihat/mengedit transaksi langsung dari dashboard
                // Asumsi Anda punya TransaksiResource, kita bisa arahkan ke sana
                Tables\Actions\Action::make('lihat')
                    ->label('Detail')
                    ->icon('heroicon-m-eye')
                    // PERBAIKAN: Menggunakan 'transaksi' (tunggal) sesuai slug di Resource
                    ->url(fn (Transaksi $record): string => route('filament.admin.resources.transaksi.edit', $record)),
            ])
            ->paginated(false); // Matikan pagination karena hanya menampilkan 5 teratas
    }
}