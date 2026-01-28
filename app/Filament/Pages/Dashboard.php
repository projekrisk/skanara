<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // Memaksa Dashboard menggunakan 1 kolom (Full Width)
    public function getColumns(): int | string | array
    {
        return 1;
    }
}