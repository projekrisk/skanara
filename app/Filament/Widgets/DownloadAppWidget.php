<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;

class DownloadAppWidget extends Widget
{
    protected static string $view = 'filament.widgets.download-app-widget';
    
    protected static ?int $sort = 2;
    
    // Paksa Full Width
    protected int | string | array $columnSpan = 'full'; 

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    public static function canView(): bool
    {
        return Auth::check();
    }
    
    protected function getViewData(): array
    {
        return [
            'url' => SystemSetting::where('key', 'android_app_url')->value('value') ?? '#',
            'desc' => SystemSetting::where('key', 'android_app_desc')->value('value') ?? 'Download aplikasi sekarang.',
            'version' => SystemSetting::where('key', 'android_app_version')->value('value') ?? 'v1.0',
        ];
    }
}