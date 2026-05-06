<?php

namespace App\Services;

use App\Models\Declaration;
use App\Models\Payroll;

class DeclarationReportService
{
    /**
     * Agrège les données de paie pour la période et met à jour la déclaration.
     * Retourne les totaux calculés.
     */
    public function generate(Declaration $declaration): array
    {
        $payrolls = Payroll::withoutGlobalScopes()
            ->where('company_id', $declaration->company_id)
            ->where('month', $declaration->month)
            ->where('year', $declaration->year)
            ->whereIn('status', ['validé', 'payé'])
            ->get();

        $totals = [
            'employee_count'       => $payrolls->count(),
            'total_brut'           => $payrolls->sum('salaire_brut'),
            'total_cnss_employee'  => $payrolls->sum('total_cnss_employee'),
            'total_cnss_employer'  => $payrolls->sum('total_cnss_employer'),
            'total_amo_employee'   => $payrolls->sum('amo_employee'),
            'total_amo_employer'   => $payrolls->sum('amo_employer'),
            'total_ir'             => $payrolls->sum('ir'),
            'total_net'            => $payrolls->sum('salaire_net'),
        ];

        $declaration->update(array_merge($totals, ['status' => 'générée']));

        return $totals;
    }

    /**
     * Vérifie s'il y a des fiches non validées dans la période.
     * Retourne le nombre de brouillons restants.
     */
    public function countDraftPayrolls(int $companyId, int $month, int $year): int
    {
        return Payroll::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'brouillon')
            ->count();
    }
}
