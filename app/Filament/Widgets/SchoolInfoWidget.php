<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SchoolInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.school-info-widget';
    
    protected static ?int $sort = 1;
    
    // Paksa Full Width
    protected int | string | array $columnSpan = 'full'; 

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->sekolah_id !== null;
    }
}