<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SchoolInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.school-info-widget';
    
    // Tampil Paling Atas
    protected static ?int $sort = 1;
    
    // Widget melebar penuh (Full Width)
    protected int | string | array $columnSpan = 'full'; 

    public static function canView(): bool
    {
        // Hanya untuk Admin Sekolah
        return Auth::check() && Auth::user()->sekolah_id !== null;
    }
}