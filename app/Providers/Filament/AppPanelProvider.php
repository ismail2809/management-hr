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
                fn () => new HtmlString('<link rel="stylesheet" href="' . asset('css/hr-theme.css') . '?v=9">')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => new HtmlString('<style>
/* ── Sidebar override — BODY_END — highest priority ── */
.fi-sidebar, .fi-main-sidebar { background-color: #0f1c3f !important; }
.fi-sidebar-nav { background-color: transparent !important; }
.fi-sidebar-item-btn {
    color: rgba(255,255,255,0.82) !important;
    border-radius: 6px !important;
    margin-inline: 8px !important;
    padding: 8px 10px !important;
    font-size: 0.82rem !important;
    font-weight: 500 !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    transition: all 0.15s !important;
}
.fi-sidebar-item-btn:hover {
    background-color: rgba(96,165,250,0.15) !important;
    color: #93c5fd !important;
}
li.fi-active > .fi-sidebar-item-btn,
.fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
    background-color: rgba(96,165,250,0.20) !important;
    color: #60a5fa !important;
    font-weight: 600 !important;
}
.fi-sidebar-item-label { color: inherit !important; }
.fi-sidebar-item-icon svg { color: inherit !important; width:16px !important; height:16px !important; }
.fi-sidebar-group-label {
    color: rgba(148,163,184,0.6) !important;
    font-size: 0.6rem !important;
    font-weight: 700 !important;
    letter-spacing: 1.4px !important;
    text-transform: uppercase !important;
}
.fi-sidebar-group-btn { color: rgba(255,255,255,0.6) !important; }
</style>')
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
