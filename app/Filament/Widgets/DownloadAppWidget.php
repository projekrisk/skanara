<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;

class DownloadAppWidget extends Widget
{
    protected static string $view = 'filament.widgets.download-app-widget';
    
    // Urutan tampilan (paling atas atau setelah stats)
    protected static ?int $sort = 2;
    
    // UBAH: Agar widget melebar penuh (Full Width)
    protected int | string | array $columnSpan = 'full'; 

    // Tampilkan untuk Admin Sekolah & Super Admin
    public static function canView(): bool
    {
        return Auth::check();
    }
    
    // Kirim data ke view
    protected function getViewData(): array
    {
        return [
            'url' => SystemSetting::where('key', 'android_app_url')->value('value') ?? '#',
            'desc' => SystemSetting::where('key', 'android_app_desc')->value('value') ?? 'Download aplikasi sekarang.',
            'version' => SystemSetting::where('key', 'android_app_version')->value('value') ?? 'v1.0',
        ];
    }
}