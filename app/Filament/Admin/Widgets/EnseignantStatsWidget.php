<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DocumentRequest;
use App\Models\Leave;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EnseignantStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['enseignant', 'enseignante']);
    }

    protected function getStats(): array
    {
        $user       = auth()->user();
        $employeeId = $user?->employee_id;
        $year       = Carbon::now()->year;

        $congesPris = Leave::where('employee_id', $employeeId)
            ->where('categorie', 'conge')
            ->where('status', 'approuvé')
            ->whereYear('start_date', $year)
            ->sum('duration_days');

        $absences = Leave::where('employee_id', $employeeId)
            ->where('categorie', 'absence')
            ->where('status', 'approuvé')
            ->whereYear('start_date', $year)
            ->count();

        $demandesEnAttente = DocumentRequest::where('employee_id', $employeeId)
            ->where('status', 'en_attente')
            ->count();

        $congesEnAttente = Leave::where('employee_id', $employeeId)
            ->where('status', 'en_attente')
            ->count();

        return [
            Stat::make('Congés pris ' . $year, $congesPris . ' j')
                ->description('Jours de congé approuvés cette année')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('info'),

            Stat::make('Absences ' . $year, $absences)
                ->description('Absences enregistrées cette année')
                ->descriptionIcon('heroicon-o-clock')
                ->color($absences > 0 ? 'warning' : 'success'),

            Stat::make('Demandes en cours', $demandesEnAttente + $congesEnAttente)
                ->description($demandesEnAttente . ' doc(s) · ' . $congesEnAttente . ' congé(s)')
                ->descriptionIcon('heroicon-o-inbox')
                ->color($demandesEnAttente + $congesEnAttente > 0 ? 'warning' : 'success'),
        ];
    }
}
