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
        $moisCourant  = now()->month;
        $annee        = now()->year;
        $moisPrecedent = now()->subMonth()->month;
        $anneePrev    = now()->subMonth()->year;
        $moisLabel    = now()->subMonth()->format('m/Y');

        // Effectif actif
        $totalEmployes = Employee::where('status', 'actif')->count();

        // Congés en attente
        $congesEnAttente = Leave::where('status', 'en_attente')->count();

        // Fiches brouillon mois courant
        $paiesBrouillon = Payroll::where('status', 'brouillon')
            ->where('month', $moisCourant)
            ->where('year', $annee)
            ->count();

        // Masse salariale brute mois précédent (validé + payé)
        $masseBrute = Payroll::whereIn('status', ['validé', 'payé'])
            ->where('month', $moisPrecedent)
            ->where('year', $anneePrev)
            ->sum('salaire_brut');

        // Total CNSS salarié mois précédent
        $totalCnss = Payroll::whereIn('status', ['validé', 'payé'])
            ->where('month', $moisPrecedent)
            ->where('year', $anneePrev)
            ->sum('total_cnss_employee');

        // Total IR mois précédent
        $totalIr = Payroll::whereIn('status', ['validé', 'payé'])
            ->where('month', $moisPrecedent)
            ->where('year', $anneePrev)
            ->sum('ir');

        // Taux absentéisme : jours d'absence sans solde approuvés / (effectif × jours ouvrables)
        $absenceDays = Payroll::whereIn('status', ['validé', 'payé'])
            ->where('month', $moisPrecedent)
            ->where('year', $anneePrev)
            ->sum('absence_days');

        $totalWorkingDays = Payroll::whereIn('status', ['validé', 'payé'])
            ->where('month', $moisPrecedent)
            ->where('year', $anneePrev)
            ->avg('total_working_days') ?? 22;

        $tauxAbsenteisme = $totalEmployes > 0
            ? round(($absenceDays / max(1, $totalEmployes * $totalWorkingDays)) * 100, 1)
            : 0;

        return [
            Stat::make('Effectif actif', $totalEmployes)
                ->description('Employés avec statut actif')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Congés en attente', $congesEnAttente)
                ->description('À approuver ou refuser')
                ->descriptionIcon('heroicon-o-clock')
                ->color($congesEnAttente > 0 ? 'warning' : 'success'),

            Stat::make('Masse salariale brute', number_format($masseBrute, 0, ',', ' ') . ' MAD')
                ->description('Mois ' . $moisLabel . ' (validé/payé)')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Total CNSS salarié', number_format($totalCnss, 0, ',', ' ') . ' MAD')
                ->description('Mois ' . $moisLabel)
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('info'),

            Stat::make('Total IR', number_format($totalIr, 0, ',', ' ') . ' MAD')
                ->description('Mois ' . $moisLabel)
                ->descriptionIcon('heroicon-o-receipt-percent')
                ->color('warning'),

            Stat::make('Taux absentéisme', $tauxAbsenteisme . '%')
                ->description('Sans solde — mois ' . $moisLabel)
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($tauxAbsenteisme > 5 ? 'danger' : ($tauxAbsenteisme > 2 ? 'warning' : 'success')),

            Stat::make('Fiches brouillon', $paiesBrouillon)
                ->description('Mois ' . now()->format('m/Y') . ' à valider')
                ->descriptionIcon('heroicon-o-document')
                ->color($paiesBrouillon > 0 ? 'danger' : 'success'),
        ];
    }
}
