<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Declaration;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Position;
use App\Services\PayrollCalculator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('name', 'Les Écoles Al Baraime')->first();
        if (!$company) {
            $this->command->error('Lance d\'abord migrate:fresh --seed pour créer la company de test.');
            return;
        }

        $cid = $company->id;

        // --- Departments ---
        $depts = [];
        foreach ([
            'Permanents',
            'Vacataires',
            'Agents de services',
            'Agents administratifs',
        ] as $name) {
            $depts[$name] = Department::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $cid, 'name' => $name]
            );
        }

        // --- Positions ---
        $positions = [];
        $posData = [
            ['title' => 'Professeur Permanent',    'base_salary' => 12000, 'dept' => 'Permanents'],
            ['title' => 'Professeur Vacataire',    'base_salary' => 6000,  'dept' => 'Vacataires'],
            ['title' => 'Agent de Sécurité',       'base_salary' => 4500,  'dept' => 'Agents de services'],
            ['title' => 'Agent d\'Entretien',      'base_salary' => 4000,  'dept' => 'Agents de services'],
            ['title' => 'Secrétaire',              'base_salary' => 6500,  'dept' => 'Agents administratifs'],
            ['title' => 'Comptable',               'base_salary' => 9000,  'dept' => 'Agents administratifs'],
            ['title' => 'Directeur Administratif', 'base_salary' => 18000, 'dept' => 'Agents administratifs'],
            ['title' => 'Technicien',              'base_salary' => 7000,  'dept' => 'Agents de services'],
        ];

        foreach ($posData as $p) {
            $positions[$p['title']] = Position::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $cid, 'title' => $p['title']],
                ['base_salary' => $p['base_salary']]
            );
        }

        // --- Employees ---
        $employeeData = [
            ['first_name' => 'Fatima',    'last_name' => 'El Amrani',    'cin' => 'BK123456', 'cnss' => 'CNSS001', 'contract_type' => 'CDI',    'marital_status' => 'marie', 'children' => 2, 'position' => 'Responsable RH',       'dept' => 'Ressources Humaines',    'hire_date' => '2020-03-01'],
            ['first_name' => 'Youssef',   'last_name' => 'Benali',        'cin' => 'BK234567', 'cnss' => 'CNSS002', 'contract_type' => 'CDI',    'marital_status' => 'celibataire',  'children' => 0, 'position' => 'Développeur Senior',    'dept' => 'Informatique',           'hire_date' => '2019-06-15'],
            ['first_name' => 'Aicha',     'last_name' => 'Karimi',        'cin' => 'BK345678', 'cnss' => 'CNSS003', 'contract_type' => 'CDI',    'marital_status' => 'marie', 'children' => 1, 'position' => 'Chef de Projet IT',     'dept' => 'Informatique',           'hire_date' => '2018-01-10'],
            ['first_name' => 'Omar',      'last_name' => 'Tahiri',        'cin' => 'BK456789', 'cnss' => 'CNSS004', 'contract_type' => 'CDI',    'marital_status' => 'marie', 'children' => 3, 'position' => 'Directeur Financier',   'dept' => 'Finance & Comptabilité', 'hire_date' => '2017-09-01'],
            ['first_name' => 'Nadia',     'last_name' => 'Chraibi',       'cin' => 'BK567890', 'cnss' => 'CNSS005', 'contract_type' => 'CDI',    'marital_status' => 'celibataire',  'children' => 0, 'position' => 'Comptable',             'dept' => 'Finance & Comptabilité', 'hire_date' => '2021-02-01'],
            ['first_name' => 'Karim',     'last_name' => 'Mansouri',      'cin' => 'BK678901', 'cnss' => 'CNSS006', 'contract_type' => 'CDI',    'marital_status' => 'marie', 'children' => 2, 'position' => 'Commercial Senior',     'dept' => 'Commercial',             'hire_date' => '2020-07-01'],
            ['first_name' => 'Sanae',     'last_name' => 'Bouazza',       'cin' => 'BK789012', 'cnss' => 'CNSS007', 'contract_type' => 'CDD',    'marital_status' => 'celibataire',  'children' => 0, 'position' => 'Commercial Junior',     'dept' => 'Commercial',             'hire_date' => '2023-01-15'],
            ['first_name' => 'Hassan',    'last_name' => 'El Idrissi',    'cin' => 'BK890123', 'cnss' => 'CNSS008', 'contract_type' => 'CDI',    'marital_status' => 'marie', 'children' => 4, 'position' => 'Responsable Production','dept' => 'Production',             'hire_date' => '2016-05-20'],
            ['first_name' => 'Zineb',     'last_name' => 'Alaoui',        'cin' => 'BK901234', 'cnss' => 'CNSS009', 'contract_type' => 'Stage',  'marital_status' => 'celibataire',  'children' => 0, 'position' => 'Développeur Junior',    'dept' => 'Informatique',           'hire_date' => '2024-09-01'],
            ['first_name' => 'Mehdi',     'last_name' => 'Zouiten',       'cin' => 'BK012345', 'cnss' => 'CNSS010', 'contract_type' => 'CDI',    'marital_status' => 'celibataire',  'children' => 0, 'position' => 'Opérateur',             'dept' => 'Production',             'hire_date' => '2022-04-01'],
            ['first_name' => 'Laila',     'last_name' => 'Benchekroun',   'cin' => 'BK112233', 'cnss' => 'CNSS011', 'contract_type' => 'CDI',    'marital_status' => 'marie', 'children' => 2, 'position' => 'Assistant RH',          'dept' => 'Ressources Humaines',    'hire_date' => '2023-06-01'],
            ['first_name' => 'Rachid',    'last_name' => 'Oujda',         'cin' => 'BK223344', 'cnss' => 'CNSS012', 'contract_type' => 'ANAPEC', 'marital_status' => 'celibataire',  'children' => 0, 'position' => 'Opérateur',             'dept' => 'Production',             'hire_date' => '2024-03-01'],
        ];

        $employees = [];
        foreach ($employeeData as $i => $e) {
            $emp = Employee::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $cid, 'cin' => $e['cin']],
                [
                    'matricule'          => 'EMP' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'cnss_number'        => $e['cnss'],
                    'first_name'         => $e['first_name'],
                    'last_name'          => $e['last_name'],
                    'email'              => strtolower($e['first_name'] . '.' . $e['last_name'] . '@societe-test.ma'),
                    'phone'              => '+2126' . rand(10000000, 99999999),
                    'birth_date'         => Carbon::now()->subYears(rand(25, 50))->subDays(rand(0, 365)),
                    'hire_date'          => $e['hire_date'],
                    'contract_type'      => $e['contract_type'],
                    'marital_status'     => $e['marital_status'],
                    'number_of_children' => $e['children'],
                    'department_id'      => $depts[$e['dept']]->id,
                    'position_id'        => $positions[$e['position']]->id,
                    'rib'                => 'MA' . rand(100000000000000000, 999999999999999999),
                    'status'             => 'actif',
                ]
            );
            $employees[] = $emp;
        }

        // --- Contrats ---
        foreach ($employees as $emp) {
            $existing = Contract::withoutGlobalScopes()->where('company_id', $cid)->where('employee_id', $emp->id)->exists();
            if (!$existing) {
                $hireDate = Carbon::parse($emp->hire_date);
                $isCdd    = $emp->contract_type === 'CDD';
                $isStage  = $emp->contract_type === 'Stage';
                $isAnapec = $emp->contract_type === 'ANAPEC';

                $endDate = null;
                if ($isCdd)    { $endDate = $hireDate->copy()->addYear(); }
                if ($isStage)  { $endDate = $hireDate->copy()->addMonths(6); }
                if ($isAnapec) { $endDate = $hireDate->copy()->addMonths(24); }

                Contract::create([
                    'company_id'             => $cid,
                    'employee_id'            => $emp->id,
                    'contract_type'          => $emp->contract_type,
                    'start_date'             => $hireDate,
                    'end_date'               => $endDate,
                    'salary_base'            => $emp->position->base_salary ?? 5000,
                    'trial_period_end'       => $hireDate->copy()->addMonths(3),
                    'working_hours_per_week' => 44,
                    'status'                 => 'actif',
                ]);
            }
        }

        // --- Paie (3 derniers mois) via PayrollCalculator ---
        $calculator = new PayrollCalculator();
        $months = [
            ['month' => 2, 'year' => 2026],
            ['month' => 3, 'year' => 2026],
            ['month' => 4, 'year' => 2026],
        ];

        $primesByPosition = [
            'Directeur Financier'    => [['type' => 'prime', 'label' => 'Prime de responsabilité', 'amount' => 3000, 'taxable' => true]],
            'Chef de Projet IT'      => [['type' => 'prime', 'label' => 'Prime de projet', 'amount' => 2000, 'taxable' => true]],
            'Développeur Senior'     => [['type' => 'prime', 'label' => 'Prime technique', 'amount' => 1500, 'taxable' => true]],
            'Commercial Senior'      => [['type' => 'prime', 'label' => 'Commission ventes', 'amount' => 2500, 'taxable' => true]],
            'Responsable Production' => [['type' => 'prime', 'label' => 'Prime de rendement', 'amount' => 1200, 'taxable' => true]],
        ];

        foreach ($employees as $emp) {
            $salaire = $emp->position->base_salary ?? 5000;
            $posTitle = $emp->position->title ?? '';
            $comps    = $primesByPosition[$posTitle] ?? [];

            foreach ($months as $period) {
                $existing = \App\Models\Payroll::withoutGlobalScopes()
                    ->where('company_id', $cid)
                    ->where('employee_id', $emp->id)
                    ->where('month', $period['month'])
                    ->where('year', $period['year'])
                    ->exists();

                if (!$existing) {
                    // Simule un user auth pour PayrollCalculator (company_id passé manuellement)
                    $payroll = $calculator->calculate($emp, $period['month'], $period['year'], $salaire, $comps);

                    // Marquer payé pour les 2 premiers mois, brouillon pour le dernier
                    $status = $period['month'] < 4 ? 'payé' : 'brouillon';
                    \App\Models\Payroll::withoutGlobalScopes()->where('id', $payroll->id)->update(['status' => $status]);
                }
            }
        }

        // --- Présences (30 derniers jours ouvrables) ---
        $today = Carbon::today();
        foreach ($employees as $emp) {
            for ($d = 29; $d >= 0; $d--) {
                $date = $today->copy()->subDays($d);
                if ($date->isWeekend()) { continue; }

                $existing = Attendance::withoutGlobalScopes()
                    ->where('company_id', $cid)
                    ->where('employee_id', $emp->id)
                    ->where('date', $date->toDateString())
                    ->exists();

                if (!$existing) {
                    // 90% présents, 10% absents
                    if (rand(1, 10) <= 9) {
                        $checkIn  = '08:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT) . ':00';
                        // 20% font des heures sup
                        $checkOut = rand(1, 5) === 1
                            ? '18:' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00'
                            : '17:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT) . ':00';

                        Attendance::create([
                            'company_id'  => $cid,
                            'employee_id' => $emp->id,
                            'date'        => $date->toDateString(),
                            'check_in'    => $checkIn,
                            'check_out'   => $checkOut,
                        ]);
                    }
                }
            }
        }

        // --- Congés ---
        $leaveTypes = LeaveType::withoutGlobalScopes()->where('company_id', $cid)->get();
        $annuel     = $leaveTypes->firstWhere('name', 'Congé annuel');
        $maladie    = $leaveTypes->firstWhere('name', 'Congé maladie');
        $rh = \App\Models\User::where('email', 'rh@societe-test.ma')->first();

        $leavesData = [
            // Approuvés
            ['emp_idx' => 1, 'type' => $annuel,  'start' => '2026-01-06', 'end' => '2026-01-10', 'status' => 'approuvé',   'reason' => 'Vacances familiales'],
            ['emp_idx' => 3, 'type' => $annuel,  'start' => '2026-02-16', 'end' => '2026-02-20', 'status' => 'approuvé',   'reason' => 'Voyage personnel'],
            ['emp_idx' => 5, 'type' => $maladie, 'start' => '2026-03-03', 'end' => '2026-03-05', 'status' => 'approuvé',   'reason' => 'Grippe'],
            ['emp_idx' => 7, 'type' => $annuel,  'start' => '2026-04-07', 'end' => '2026-04-11', 'status' => 'approuvé',   'reason' => 'Congé printanier'],
            // Refusés
            ['emp_idx' => 2, 'type' => $annuel,  'start' => '2026-03-23', 'end' => '2026-03-28', 'status' => 'refusé',     'reason' => 'Période chargée'],
            // En attente
            ['emp_idx' => 4, 'type' => $annuel,  'start' => '2026-05-12', 'end' => '2026-05-16', 'status' => 'en_attente', 'reason' => 'Congé prévu'],
            ['emp_idx' => 6, 'type' => $maladie, 'start' => '2026-05-05', 'end' => '2026-05-06', 'status' => 'en_attente', 'reason' => 'Consultation médicale'],
            ['emp_idx' => 9, 'type' => $annuel,  'start' => '2026-05-19', 'end' => '2026-05-23', 'status' => 'en_attente', 'reason' => 'Visite famille'],
        ];

        foreach ($leavesData as $ld) {
            if (!$ld['type'] || !isset($employees[$ld['emp_idx']])) { continue; }
            $emp = $employees[$ld['emp_idx']];

            $exists = Leave::withoutGlobalScopes()
                ->where('company_id', $cid)
                ->where('employee_id', $emp->id)
                ->where('start_date', $ld['start'])
                ->exists();

            if (!$exists) {
                Leave::create([
                    'company_id'   => $cid,
                    'employee_id'  => $emp->id,
                    'leave_type_id'=> $ld['type']->id,
                    'start_date'   => $ld['start'],
                    'end_date'     => $ld['end'],
                    'reason'       => $ld['reason'],
                    'status'       => $ld['status'],
                    'approved_by'  => in_array($ld['status'], ['approuvé', 'refusé']) ? $rh?->id : null,
                    'approved_at'  => in_array($ld['status'], ['approuvé', 'refusé']) ? now() : null,
                ]);
            }
        }

        // --- Déclarations ---
        $declarationsData = [
            ['type' => 'CNSS',      'month' => 1, 'year' => 2026, 'status' => 'soumise'],
            ['type' => 'IR',        'month' => 1, 'year' => 2026, 'status' => 'soumise'],
            ['type' => 'CNSS',      'month' => 2, 'year' => 2026, 'status' => 'soumise'],
            ['type' => 'IR',        'month' => 2, 'year' => 2026, 'status' => 'soumise'],
            ['type' => 'CNSS',      'month' => 3, 'year' => 2026, 'status' => 'générée'],
            ['type' => 'IR',        'month' => 3, 'year' => 2026, 'status' => 'générée'],
            ['type' => 'Etat_9421', 'month' => 3, 'year' => 2026, 'status' => 'générée'],
            ['type' => 'CNSS',      'month' => 4, 'year' => 2026, 'status' => 'en_cours'],
            ['type' => 'IR',        'month' => 4, 'year' => 2026, 'status' => 'en_cours'],
        ];

        foreach ($declarationsData as $d) {
            Declaration::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $cid, 'type' => $d['type'], 'month' => $d['month'], 'year' => $d['year']],
                ['status' => $d['status']]
            );
        }

        $this->command->info('Demo data seeded: 12 employés, contrats, 3 mois de paie, présences, congés, déclarations.');
    }
}
