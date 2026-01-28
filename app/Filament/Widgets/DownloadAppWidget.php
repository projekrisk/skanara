<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;

class DownloadAppWidget extends Widget
{
    protected static string $view = 'filament.widgets.download-app-widget';
    
    // Urutan tampilan (sesuaikan agar bersebelahan dengan stats)
    protected static ?int $sort = 2;
    
    // Agar widget melebar memenuhi ruang yang tersedia (opsional, tergantung grid)
    protected int | string | array $columnSpan = 1; 

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
