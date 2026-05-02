<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CnssRate;
use App\Models\Employee;
use App\Models\IrBracket;
use App\Models\Payroll;
use App\Models\PayrollComponent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class PayrollCalculator
{
    // Taux légaux Maroc 2024
    private const CNSS_EMPLOYEE_RATE   = 4.48;
    private const CNSS_EMPLOYER_RATE   = 10.77;
    private const CNSS_PLAFOND         = 6000.00;
    private const AMO_EMPLOYEE_RATE    = 2.26;
    private const AMO_EMPLOYER_RATE    = 4.11;
    private const FRAIS_PRO_RATE       = 20.0;
    private const FRAIS_PRO_PLAFOND_AN = 30000.0;

    // Heures mensuelles normales (44h/semaine × 52 / 12)
    private const HEURES_MENSUELLES    = 191.33;

    // Majorations heures sup (Maroc)
    private const HS_TAUX_JOUR        = 1.25; // +25% jours normaux
    private const HS_TAUX_NUIT        = 1.50; // +50% nuit / repos

    /**
     * Calcule les montants sans persister (prévisualisation formulaire).
     */
    public function computeValues(
        Employee $employee,
        float $salaireBrut,
        array $components = [],
        float $overtimeHours = 0,
        bool $isProrata = false,
        ?int $workedDays = null,
        ?int $totalWorkingDays = null
    ): array {
        $rates = $this->loadRates($employee->company_id);

        // Taux horaire
        $tauxHoraire   = $salaireBrut / self::HEURES_MENSUELLES;
        $overtimeAmount = round($overtimeHours * $tauxHoraire * self::HS_TAUX_JOUR, 2);

        // Prorata sur salaire de base uniquement
        $salaireBrutEffectif = $salaireBrut;
        if ($isProrata && $workedDays > 0 && $totalWorkingDays > 0) {
            $salaireBrutEffectif = round($salaireBrut * $workedDays / $totalWorkingDays, 2);
        }

        $primesImposables = collect($components)->where('type', 'prime')->where('taxable', true)->sum('amount');
        $retenuesSup      = collect($components)->where('type', 'retenue')->sum('amount');
        $brutImposable    = $salaireBrutEffectif + $overtimeAmount + $primesImposables;

        $baseCnss     = $rates['cnss_plafond'] ? min($brutImposable, $rates['cnss_plafond']) : $brutImposable;
        $cnssEmployee = round($baseCnss * $rates['cnss_employee'] / 100, 2);
        $cnssEmployer = round($baseCnss * $rates['cnss_employer'] / 100, 2);
        $amoEmployee  = round($brutImposable * $rates['amo_employee'] / 100, 2);
        $amoEmployer  = round($brutImposable * $rates['amo_employer'] / 100, 2);

        $fraisProMensuel = min($brutImposable * self::FRAIS_PRO_RATE / 100, self::FRAIS_PRO_PLAFOND_AN / 12);
        $baseIr          = max(0, $brutImposable - $cnssEmployee - $fraisProMensuel);
        $ir              = $this->calculateIr($baseIr, $employee->number_of_children ?? 0);
        $salaireNet      = round($brutImposable - $cnssEmployee - $amoEmployee - $ir - $retenuesSup, 2);

        return compact(
            'salaireBrutEffectif', 'overtimeAmount',
            'cnssEmployee', 'cnssEmployer',
            'amoEmployee', 'amoEmployer',
            'ir', 'salaireNet'
        );
    }

    /**
     * Calcule et persiste une fiche de paie.
     */
    public function calculate(
        Employee $employee,
        int $month,
        int $year,
        float $salaireBrut,
        array $components = [],
        ?float $overtimeHours = null,
        bool $isProrata = false,
        ?int $workedDays = null,
        ?int $totalWorkingDays = null
    ): Payroll {
        $rates = $this->loadRates($employee->company_id);

        // Auto-fetch heures sup depuis Attendance si non fourni
        if ($overtimeHours === null) {
            $overtimeHours = $this->fetchOvertimeHours($employee->id, $month, $year);
        }

        // Taux horaire & heures sup
        $tauxHoraire    = $salaireBrut / self::HEURES_MENSUELLES;
        $overtimeAmount = round($overtimeHours * $tauxHoraire * self::HS_TAUX_JOUR, 2);

        // Prorata
        $salaireBrutEffectif = $salaireBrut;
        if ($isProrata && $workedDays > 0 && $totalWorkingDays > 0) {
            $salaireBrutEffectif = round($salaireBrut * $workedDays / $totalWorkingDays, 2);
        }
        if ($totalWorkingDays === null) {
            $totalWorkingDays = $this->countWorkingDays($month, $year);
        }

        $primesImposables = collect($components)->where('type', 'prime')->where('taxable', true)->sum('amount');
        $retenuesSup      = collect($components)->where('type', 'retenue')->sum('amount');
        $brutImposable    = $salaireBrutEffectif + $overtimeAmount + $primesImposables;

        $baseCnss     = $rates['cnss_plafond'] ? min($brutImposable, $rates['cnss_plafond']) : $brutImposable;
        $cnssEmployee = round($baseCnss * $rates['cnss_employee'] / 100, 2);
        $cnssEmployer = round($baseCnss * $rates['cnss_employer'] / 100, 2);
        $amoEmployee  = round($brutImposable * $rates['amo_employee'] / 100, 2);
        $amoEmployer  = round($brutImposable * $rates['amo_employer'] / 100, 2);

        $fraisProMensuel = min($brutImposable * self::FRAIS_PRO_RATE / 100, self::FRAIS_PRO_PLAFOND_AN / 12);
        $baseIr          = max(0, $brutImposable - $cnssEmployee - $fraisProMensuel);
        $ir              = $this->calculateIr($baseIr, $employee->number_of_children ?? 0);
        $salaireNet      = round($brutImposable - $cnssEmployee - $amoEmployee - $ir - $retenuesSup, 2);

        $payroll = Payroll::updateOrCreate(
            ['company_id' => $employee->company_id, 'employee_id' => $employee->id, 'month' => $month, 'year' => $year],
            [
                'salaire_brut'         => $salaireBrut,
                'overtime_hours'       => $overtimeHours,
                'overtime_amount'      => $overtimeAmount,
                'is_prorata'           => $isProrata,
                'worked_days'          => $workedDays,
                'total_working_days'   => $totalWorkingDays,
                'total_cnss_employee'  => $cnssEmployee,
                'total_cnss_employer'  => $cnssEmployer,
                'amo_employee'         => $amoEmployee,
                'amo_employer'         => $amoEmployer,
                'ir'                   => $ir,
                'salaire_net'          => $salaireNet,
                'status'               => 'brouillon',
            ]
        );

        $payroll->components()->delete();
        foreach ($components as $comp) {
            PayrollComponent::create([
                'payroll_id' => $payroll->id,
                'type'       => $comp['type'],
                'label'      => $comp['label'],
                'amount'     => $comp['amount'],
                'taxable'    => $comp['taxable'] ?? true,
            ]);
        }

        return $payroll->fresh('components');
    }

    /**
     * Somme des overtime_hours depuis Attendance pour le mois.
     */
    public function fetchOvertimeHours(int $employeeId, int $month, int $year): float
    {
        return (float) Attendance::withoutGlobalScopes()
            ->where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('overtime_hours');
    }

    /**
     * Nombre de jours ouvrables dans un mois (lundi–vendredi, hors week-ends).
     */
    public function countWorkingDays(int $month, int $year): int
    {
        $start  = Carbon::createFromDate($year, $month, 1);
        $end    = $start->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        return collect($period)->filter(fn ($d) => ! $d->isWeekend())->count();
    }

    private function loadRates(int $companyId): array
    {
        $dbRates = CnssRate::withoutGlobalScopes()
            ->where(fn ($q) => $q->whereNull('company_id')->orWhere('company_id', $companyId))
            ->get();

        $resolve = fn (string $type, string $label, float $default) =>
            (float) ($dbRates->first(fn ($r) => $r->type === $type && $r->label === $label && $r->company_id == $companyId)?->rate_percentage
                ?? $dbRates->first(fn ($r) => $r->type === $type && $r->label === $label && is_null($r->company_id))?->rate_percentage
                ?? $default);

        $resolvePlafond = fn (string $type, string $label, ?float $default) =>
            ($dbRates->first(fn ($r) => $r->type === $type && $r->label === $label && $r->company_id == $companyId)?->plafond
                ?? $dbRates->first(fn ($r) => $r->type === $type && $r->label === $label && is_null($r->company_id))?->plafond
                ?? $default) ?: $default;

        return [
            'cnss_employee' => $resolve('employee', 'CNSS', self::CNSS_EMPLOYEE_RATE),
            'cnss_employer' => $resolve('employer', 'CNSS', self::CNSS_EMPLOYER_RATE),
            'cnss_plafond'  => $resolvePlafond('employee', 'CNSS', self::CNSS_PLAFOND),
            'amo_employee'  => $resolve('employee', 'AMO', self::AMO_EMPLOYEE_RATE),
            'amo_employer'  => $resolve('employer', 'AMO', self::AMO_EMPLOYER_RATE),
        ];
    }

    private function calculateIr(float $baseIrMensuel, int $nbEnfants): float
    {
        $baseIrAnnuel     = $baseIrMensuel * 12;
        $brackets         = IrBracket::orderBy('min_salary')->get();
        if ($brackets->isEmpty()) { $brackets = $this->defaultBrackets(); }

        $irAnnuel         = $this->applyBrackets($baseIrAnnuel, $brackets);
        $deductionFamille = min($nbEnfants, 6) * 360;

        return round(max(0, $irAnnuel - $deductionFamille) / 12, 2);
    }

    private function applyBrackets(float $base, $brackets): float
    {
        foreach ($brackets as $b) {
            $max = $b->max_salary ?? PHP_FLOAT_MAX;
            if ($base >= $b->min_salary && $base <= $max) {
                return ($base * $b->rate_percentage / 100) - $b->deduction_amount;
            }
        }
        return 0;
    }

    private function defaultBrackets(): Collection
    {
        return collect([
            ['min_salary' => 0,      'max_salary' => 30000,  'rate_percentage' => 0,  'deduction_amount' => 0],
            ['min_salary' => 30001,  'max_salary' => 50000,  'rate_percentage' => 10, 'deduction_amount' => 3000],
            ['min_salary' => 50001,  'max_salary' => 60000,  'rate_percentage' => 20, 'deduction_amount' => 8000],
            ['min_salary' => 60001,  'max_salary' => 80000,  'rate_percentage' => 30, 'deduction_amount' => 14000],
            ['min_salary' => 80001,  'max_salary' => 180000, 'rate_percentage' => 34, 'deduction_amount' => 17200],
            ['min_salary' => 180001, 'max_salary' => null,   'rate_percentage' => 38, 'deduction_amount' => 24400],
        ])->map(fn ($b) => (object) $b);
    }
}
