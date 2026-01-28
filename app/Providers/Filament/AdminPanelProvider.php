<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth; // Import Auth

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            
            // --- 1. BRANDING DINAMIS ---
            ->brandName(fn () => 
                (Auth::check() && Auth::user()->sekolah_id) 
                    ? Auth::user()->sekolah->nama_sekolah 
                    : 'Skanara Admin'
            )
            ->favicon(asset('favicon.png'))
            ->brandLogo(fn () => 
                (Auth::check() && Auth::user()->sekolah_id && Auth::user()->sekolah->logo)
                    ? asset('uploads/' . Auth::user()->sekolah->logo)
                    : asset('favicon.png') // Default Logo Skanara
            )
            ->brandLogoHeight('3rem')
            // ---------------------------

            // --- 2. GLOBAL SEARCH ---
            // Pastikan global search aktif (default true, tapi pastikan resource dikonfigurasi)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k']) 
            
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Gunakan Custom Dashboard (Tahap 36) atau default
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\CheckSchoolSubscription::class, 
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}