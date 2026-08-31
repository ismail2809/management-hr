<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\App\Pages\Dashboard;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use App\Filament\App\Widgets\HrStatsOverview;
use App\Filament\App\Widgets\LeavesWidget;
use App\Filament\App\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('admin')
            ->login(\App\Filament\App\Pages\Auth\Login::class)
            ->profile(isSimple: false)
            ->colors([
                'primary'  => Color::hex('#1e40af'),
                'warning'  => Color::hex('#f59e0b'),
                'success'  => Color::hex('#059669'),
                'danger'   => Color::hex('#dc2626'),
                'info'     => Color::hex('#0284c7'),
                'gray'     => Color::Slate,
            ])
            ->brandName('École RH')
            ->maxContentWidth(Width::Full)
            ->sidebarWidth('230px')
            ->sidebarCollapsibleOnDesktop()
            ->favicon(asset('images/logo.svg'))
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn () => new HtmlString('<link rel="stylesheet" href="' . asset('css/hr-theme.css') . '?v=5">')
            )
            ->navigationGroups([
                NavigationGroup::make('Personnel')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make('Congés & Absences')
                    ->icon('heroicon-o-calendar-days'),
                NavigationGroup::make('Demandes')
                    ->icon('heroicon-o-document-text'),
                NavigationGroup::make('Administration')
                    ->icon('heroicon-o-cog-6-tooth'),
                NavigationGroup::make('Paramétrage')
                    ->icon('heroicon-o-adjustments-horizontal'),
                NavigationGroup::make('Journal d\'audit')
                    ->icon('heroicon-o-clipboard-document-list'),
            ])
            ->navigationItems([
                NavigationItem::make('Mon profil')
                    ->icon('heroicon-o-user-circle')
                    ->sort(2)
                    ->url(fn () => ($emp = auth()->user()?->employee)
                        ? \App\Filament\App\Resources\EmployeeResource::getUrl('view', ['record' => $emp])
                        : '#')
                    ->visible(fn () => auth()->user()?->hasRole('employee') && auth()->user()?->employee_id),
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                AccountWidget::class,
                HrStatsOverview::class,
                LeavesWidget::class,
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
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\RequireAppRole::class,
            ]);
    }
}
