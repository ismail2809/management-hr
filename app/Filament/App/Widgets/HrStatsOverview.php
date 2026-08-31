<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\EmployeeResource;
use App\Filament\App\Resources\LeaveResource;
use App\Models\Employee;
use App\Models\Leave;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    protected function getStats(): array
    {
        $totalActifs       = Employee::where('status', 'actif')->count();
        $totalInactifs     = Employee::where('status', '!=', 'actif')->count();
        $congesEnAttente   = Leave::where('status', 'en_attente')->count();
        $absentsAujourdhui = Leave::where('status', 'approuvé')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();
        $congesCeMois      = Leave::where('status', 'approuvé')
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->count();

        $tauxPresence = $totalActifs > 0
            ? round((($totalActifs - $absentsAujourdhui) / $totalActifs) * 100)
            : 100;

        return [
            Stat::make('Effectif actif', $totalActifs)
                ->description($totalInactifs . ' inactif' . ($totalInactifs > 1 ? 's' : ''))
                ->descriptionIcon('heroicon-o-users')
                ->color('primary')
                ->url(EmployeeResource::getUrl('index') . '?tableFilters[status][value]=actif'),

            Stat::make('Congés en attente', $congesEnAttente)
                ->description($congesEnAttente > 0 ? 'À traiter rapidement' : 'Aucune demande')
                ->descriptionIcon($congesEnAttente > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->color($congesEnAttente > 0 ? 'warning' : 'success')
                ->url(LeaveResource::getUrl('index') . '?tableFilters[status][value]=en_attente'),

            Stat::make("Absents aujourd'hui", $absentsAujourdhui)
                ->description($absentsAujourdhui > 0 ? 'Absences approuvées' : 'Tout le monde est présent')
                ->descriptionIcon($absentsAujourdhui > 0 ? 'heroicon-o-user-minus' : 'heroicon-o-user-group')
                ->color($absentsAujourdhui > 0 ? 'danger' : 'success')
                ->url(LeaveResource::getUrl('index') . '?tableFilters[status][value]=approuv%C3%A9'),

            Stat::make('Taux de présence', $tauxPresence . '%')
                ->description('Aujourd\'hui — ' . ($totalActifs - $absentsAujourdhui) . '/' . $totalActifs . ' présents')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($tauxPresence >= 90 ? 'success' : ($tauxPresence >= 75 ? 'warning' : 'danger')),
        ];
    }
}
