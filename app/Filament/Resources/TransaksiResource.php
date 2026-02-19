<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $slug = 'transaksi';
        
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_sekolah')
                    ->relationship('sekolah', 'nama_sekolah')
                    ->label('Sekolah')
                    ->disabled(),
                Forms\Components\Select::make('id_paket')
                    ->relationship('paket', 'nama_paket')
                    ->label('Paket')
                    ->disabled(),
                Forms\Components\TextInput::make('jumlah_bayar')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\FileUpload::make('bukti_bayar')
                    ->image()
                    ->directory('bukti-bayar')
                    ->columnSpanFull()
                    ->openable(),
                Forms\Components\Select::make('status')
                    ->options([
                        'menunggu' => 'Menunggu',
                        'diproses' => 'Diproses',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->date('d M Y')->label('Tanggal'),
                Tables\Columns\TextColumn::make('sekolah.nama_sekolah')->searchable(),
                Tables\Columns\TextColumn::make('paket.nama_paket'),
                Tables\Columns\TextColumn::make('jumlah_bayar')->money('IDR'),
                Tables\Columns\ImageColumn::make('bukti_bayar'), 
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'diproses' => 'info',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                Tables\Actions\Action::make('setujui')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Transaksi $record) => $record->status !== 'disetujui' && $record->bukti_bayar)
                    ->action(function (Transaksi $record) {
                        $record->update(['status' => 'disetujui']);

                        $sekolah = $record->sekolah;
                        $paket = $record->paket;

                        $sekolah->update([
                            'status_langganan' => 'aktif',
                            'langganan_berakhir_pada' => $sekolah->langganan_berakhir_pada > now() 
                                ? \Carbon\Carbon::parse($sekolah->langganan_berakhir_pada)->addDays($paket->durasi_hari)
                                : now()->addDays($paket->durasi_hari),
                        ]);

                        Notification::make()
                            ->title('Pembayaran Disetujui')
                            ->body("Langganan {$sekolah->nama_sekolah} telah diperbarui.")
                            ->success()
                            ->send();
                    }),
                    
                 Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Transaksi $record) => $record->status !== 'disetujui' && $record->status !== 'ditolak')
                    ->action(fn (Transaksi $record) => $record->update(['status' => 'ditolak'])),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
            'edit' => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }
}
