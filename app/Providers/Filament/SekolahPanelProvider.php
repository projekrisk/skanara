<?php

namespace App\Providers\Filament;

use App\Filament\Sekolah\Auth\Login;
use App\Models\Sekolah;
use App\Http\Middleware\CheckSubscription;
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

class SekolahPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('sekolah')
            ->path('sekolah')
            ->login(Login::class)
            ->tenant(Sekolah::class)
            ->favicon(asset('favicon.png'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Sekolah/Resources'), for: 'App\\Filament\\Sekolah\\Resources')
            ->discoverPages(in: app_path('Filament/Sekolah/Pages'), for: 'App\\Filament\\Sekolah\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Sekolah/Widgets'), for: 'App\\Filament\\Sekolah\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Sekolah\Widgets\SubscriptionExpiryWidget::class,
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
                CheckSubscription::class, 
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}