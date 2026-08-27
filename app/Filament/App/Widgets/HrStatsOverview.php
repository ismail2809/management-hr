<?php

namespace App\Filament\App\Widgets;

use App\Models\Employee;
use App\Models\Leave;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    protected function getStats(): array
    {
        $totalEmployes    = Employee::where('status', 'actif')->count();
        $congesEnAttente  = Leave::where('status', 'en_attente')->count();
        $absentsAujourdhui = Leave::where('status', 'approuvé')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();

        return [
            Stat::make('Effectif actif', $totalEmployes)
                ->description('Employés avec statut actif')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Congés en attente', $congesEnAttente)
                ->description('À approuver ou refuser')
                ->descriptionIcon('heroicon-o-clock')
                ->color($congesEnAttente > 0 ? 'warning' : 'success'),

            Stat::make("Absents aujourd'hui", $absentsAujourdhui)
                ->description('Congés/absences approuvés')
                ->descriptionIcon('heroicon-o-user-minus')
                ->color($absentsAujourdhui > 0 ? 'danger' : 'success'),
        ];
    }
}
