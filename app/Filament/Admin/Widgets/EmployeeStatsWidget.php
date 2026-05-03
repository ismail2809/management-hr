<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total    = Employee::withoutGlobalScopes()->count();
        $actifs   = Employee::withoutGlobalScopes()->where('status', 'actif')->count();
        $inactifs = Employee::withoutGlobalScopes()->where('status', 'inactif')->count();
        $suspendus = Employee::withoutGlobalScopes()->where('status', 'suspendu')->count();

        return [
            Stat::make('Total employés', $total)
                ->description('Toutes entreprises confondues')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Actifs', $actifs)
                ->description(round($total > 0 ? ($actifs / $total) * 100 : 0) . '% du total')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Inactifs', $inactifs)
                ->description('Contrats terminés ou suspendus')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Suspendus', $suspendus)
                ->description('En attente de décision')
                ->icon('heroicon-o-pause-circle')
                ->color('warning'),
        ];
    }
}
