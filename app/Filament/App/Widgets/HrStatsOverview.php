<?php

namespace App\Filament\App\Widgets;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Payroll;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $companyId = auth()->user()?->company_id;

        $totalEmployes = Employee::count();

        $congesEnAttente = Leave::where('status', 'en_attente')->count();

        $paiesBrouillon = Payroll::where('status', 'brouillon')
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->count();

        $presencesAujourd = Attendance::whereDate('date', today())->count();

        $masseSalariale = Payroll::where('status', 'payé')
            ->where('month', now()->subMonth()->month)
            ->where('year', now()->subMonth()->year)
            ->sum('salaire_net');

        return [
            Stat::make('Employés actifs', $totalEmployes)
                ->description('Total dans la société')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Congés en attente', $congesEnAttente)
                ->description('À approuver ou refuser')
                ->descriptionIcon('heroicon-o-clock')
                ->color($congesEnAttente > 0 ? 'warning' : 'success'),

            Stat::make('Fiches brouillon', $paiesBrouillon)
                ->description('Mois ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-o-document')
                ->color($paiesBrouillon > 0 ? 'danger' : 'success'),

            Stat::make('Présences aujourd\'hui', $presencesAujourd)
                ->description('Pointages enregistrés')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('info'),

            Stat::make('Masse salariale nette', number_format($masseSalariale, 2, ',', ' ') . ' MAD')
                ->description('Mois ' . now()->subMonth()->translatedFormat('F Y') . ' (payé)')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}
