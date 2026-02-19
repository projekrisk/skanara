<?php

namespace App\Filament\Sekolah\Widgets;

use App\Models\PengaturanGlobal;
use Filament\Widgets\Widget;

class DownloadAplikasiWidget extends Widget
{
    // Arahkan ke view blade custom
    protected static string $view = 'filament.sekolah.widgets.download-aplikasi-widget';

    // Urutan: -2 agar muncul di atas, tepat setelah atau di samping widget Selamat Datang
    protected static ?int $sort = -2;

    // Lebar widget: Hapus 'full' agar bisa berdampingan (default 1 kolom pada grid 2)
    // protected int | string | array $columnSpan = 'full'; 

    // Kirim data ke view
    protected function getViewData(): array
    {
        return [
            'pengaturan' => PengaturanGlobal::first(),
        ];
    }

    // Cek apakah widget aktif
    public static function canView(): bool
    {
        $setting = PengaturanGlobal::first();
        return $setting && $setting->is_active;
    }
}
