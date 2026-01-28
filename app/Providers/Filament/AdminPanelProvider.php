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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString; // Import HtmlString

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            
            // --- 1. BRANDING DINAMIS (LOGO + TEKS) ---
            ->brandName('Skanara Admin') // Fallback meta title
            ->favicon(asset('favicon.png'))
            ->brandLogo(fn () => new HtmlString(
                '<div style="display: flex; align-items: center; gap: 10px;">
                    <img src="' . (
                        (Auth::check() && Auth::user()->sekolah_id && Auth::user()->sekolah->logo)
                            ? asset('uploads/' . Auth::user()->sekolah->logo)
                            : asset('favicon.png')
                    ) . '" style="height: 35px; width: auto; object-fit: contain; border-radius: 4px;" alt="Logo">
                    
                    <span style="font-weight: 700; font-size: 1.1rem; color: var(--gray-950); dark:color:white;">' . (
                        (Auth::check() && Auth::user()->sekolah_id) 
                            ? Auth::user()->sekolah->nama_sekolah 
                            : 'Skanara Admin'
                    ) . '</span>
                </div>'
            ))
            ->brandLogoHeight('auto') // Biarkan auto agar wrapper fleksibel
            // ------------------------------------------

            // --- 2. GLOBAL SEARCH ---
            ->globalSearchKeyBindings(['command+k', 'ctrl+k']) 
            
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
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