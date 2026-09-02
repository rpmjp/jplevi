<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
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
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->profile()
            ->brandName('JP Levi')
            ->colors([
                // WordPress admin blue, not the site's electric blue. This is
                // the one screen deliberately not in the public palette.
                'primary' => Color::hex('#2271b1'),
            ])
            ->font('Inter')
            // WordPress has no dark mode, and half a WordPress is worse than
            // either whole one.
            ->darkMode(false)
            // WordPress's toolbar carries a command palette and a search; this
            // is both. Resources declare a title attribute so it finds things.
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                // The site name node in WordPress's bar: a way back to the
                // thing you are editing. There was no link to it at all.
                'visit' => \Filament\Navigation\MenuItem::make()
                    ->label('Visit site')
                    ->url(fn () => url('/blog'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-arrow-top-right-on-square'),

                'profile' => \Filament\Navigation\MenuItem::make()->label('Profile'),
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::TOPBAR_START,
                fn (): string => view('filament.topbar')->render(),
            )
            // Grouped the way WordPress groups it, because that is the part
            // that makes a panel findable without being explained.
            ->navigationGroups([
                'Content',
                'Audience',
                'People',
                'Configuration',
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                \App\Filament\Admin\Widgets\ReadingStats::class,
                \App\Filament\Admin\Widgets\TopPosts::class,
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
