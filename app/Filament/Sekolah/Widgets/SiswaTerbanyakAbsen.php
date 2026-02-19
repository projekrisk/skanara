<?php

namespace App\Filament\Sekolah\Widgets;

use App\Models\Siswa;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SiswaTerbanyakAbsen extends BaseWidget
{
    protected static ?string $heading = 'Siswa Terbanyak Absen (Bulan Ini)';
    
    // Urutan widget (opsional)
    protected static ?int $sort = 3;

    // Properti ini membuat widget mengambil lebar penuh (full width)
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // Ambil Tenant dengan aman
                $tenant = Filament::getTenant();
                $sekolahId = $tenant ? $tenant->id : (Auth::user()->id_sekolah ?? null);

                if (!$sekolahId) {
                    // Jika tidak ada sekolah, return query kosong
                    return Siswa::query()->whereNull('id');
                }

                // Query Siswa dengan jumlah ketidakhadiran terbanyak bulan ini
                return Siswa::query()
                    ->where('id_sekolah', $sekolahId)
                    ->whereHas('presensi', function ($query) {
                        $query->whereMonth('tanggal', now()->month)
                              ->whereYear('tanggal', now()->year)
                              ->whereIn('status_kehadiran', ['alpa', 'sakit', 'izin', 'terlambat']);
                    })
                    ->withCount(['presensi as total_ketidakhadiran' => function ($query) {
                        $query->whereMonth('tanggal', now()->month)
                              ->whereYear('tanggal', now()->year)
                              ->whereIn('status_kehadiran', ['alpa', 'sakit', 'izin', 'terlambat']);
                    }])
                    ->orderByDesc('total_ketidakhadiran')
                    ->limit(5);
            })
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama Siswa')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas'),

                Tables\Columns\TextColumn::make('total_ketidakhadiran')
                    ->label('Total Absen')
                    ->badge()
                    ->color('danger')
                    ->alignCenter(),
            ])
            ->paginated(false); // Matikan pagination untuk widget ringkas
    }
}