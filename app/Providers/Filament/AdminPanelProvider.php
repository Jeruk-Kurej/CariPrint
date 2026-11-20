<?php

namespace App\Providers\Filament;

use App\Filament\InitialsAvatarProvider;
use Filament\Actions\Action;
use Filament\FontProviders\BunnyFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->brandName('CariPrint Admin')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.svg'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->defaultAvatarProvider(InitialsAvatarProvider::class)
            ->navigationItems([
                NavigationItem::make('Lihat website')
                    ->url('/')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->sort(100),
            ])
            ->userMenuItems([
                Action::make('site')->label('Lihat website')->url('/')->icon(Heroicon::OutlinedGlobeAlt),
                Action::make('account')->label('Akunku')->url('/akun')->icon(Heroicon::OutlinedUser),
            ])
            ->path('admin')
            ->login()
            ->darkMode(false)
            ->unsavedChangesAlerts()
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->font('Gabarito', provider: BunnyFontProvider::class)
            ->colors([
                'primary' => Color::hex('#3558f0'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
