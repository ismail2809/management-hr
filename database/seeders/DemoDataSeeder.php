<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
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

        // --- Employés ---
        $employeeData = [
            ['first_name' => 'Hassan',  'last_name' => 'El Fassi',      'cin' => 'BK100001', 'cnss' => 'CNSS001', 'contract_type' => 'CDI', 'marital_status' => 'marie',       'children' => 3, 'hire_date' => '2015-09-01'],
            ['first_name' => 'Fatima',  'last_name' => 'Cherkaoui',     'cin' => 'BK100002', 'cnss' => 'CNSS002', 'contract_type' => 'CDI', 'marital_status' => 'marie',       'children' => 2, 'hire_date' => '2016-09-01'],
            ['first_name' => 'Omar',    'last_name' => 'Bensouda',      'cin' => 'BK100003', 'cnss' => 'CNSS003', 'contract_type' => 'CDI', 'marital_status' => 'celibataire', 'children' => 0, 'hire_date' => '2018-09-01'],
            ['first_name' => 'Nadia',   'last_name' => 'Alaoui',        'cin' => 'BK100004', 'cnss' => 'CNSS004', 'contract_type' => 'CDI', 'marital_status' => 'marie',       'children' => 1, 'hire_date' => '2019-01-15'],
            ['first_name' => 'Sanae',   'last_name' => 'Tahiri',        'cin' => 'BK100005', 'cnss' => 'CNSS005', 'contract_type' => 'CDI', 'marital_status' => 'celibataire', 'children' => 0, 'hire_date' => '2020-09-01'],
            ['first_name' => 'Zineb',   'last_name' => 'Benali',        'cin' => 'BK100006', 'cnss' => 'CNSS006', 'contract_type' => 'CDI', 'marital_status' => 'marie',       'children' => 1, 'hire_date' => '2017-09-01'],
            ['first_name' => 'Youssef', 'last_name' => 'El Amrani',    'cin' => 'BK100007', 'cnss' => 'CNSS007', 'contract_type' => 'CDI', 'marital_status' => 'marie',       'children' => 2, 'hire_date' => '2018-09-01'],
            ['first_name' => 'Aicha',   'last_name' => 'Mansouri',      'cin' => 'BK100008', 'cnss' => 'CNSS008', 'contract_type' => 'CDI', 'marital_status' => 'marie',       'children' => 1, 'hire_date' => '2019-09-01'],
            ['first_name' => 'Karim',   'last_name' => 'Zouiten',       'cin' => 'BK100009', 'cnss' => 'CNSS009', 'contract_type' => 'CDI', 'marital_status' => 'marie',       'children' => 2, 'hire_date' => '2016-09-01'],
            ['first_name' => 'Khalid',  'last_name' => 'Bouzidi',       'cin' => 'BK100010', 'cnss' => 'CNSS010', 'contract_type' => 'CDI', 'marital_status' => 'marie',       'children' => 2, 'hire_date' => '2017-09-01'],
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
                    'email'              => strtolower($e['first_name'] . '.' . $e['last_name'] . '@ecole-albaraime.ma'),
                    'phone'              => '+2126' . rand(10000000, 99999999),
                    'birth_date'         => Carbon::now()->subYears(rand(28, 55))->subDays(rand(0, 365)),
                    'hire_date'          => $e['hire_date'],
                    'contract_type'      => $e['contract_type'],
                    'marital_status'     => $e['marital_status'],
                    'number_of_children' => $e['children'],
                    'rib'                => 'MA' . rand(100000000000000000, 999999999999999999),
                    'status'             => 'actif',
                ]
            );
            $employees[] = $emp;
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

                if (!$existing && rand(1, 10) <= 9) {
                    $checkIn  = '07:' . str_pad(rand(30, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    $checkOut = rand(1, 6) === 1
                        ? '17:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT) . ':00'
                        : '16:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT) . ':00';

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

        // --- Congés ---
        $leaveTypes = LeaveType::withoutGlobalScopes()->where('company_id', $cid)->get();
        $annuel     = $leaveTypes->firstWhere('name', 'Congé annuel');
        $maladie    = $leaveTypes->firstWhere('name', 'Congé maladie');
        $rh         = User::where('email', 'rh@societe-test.ma')->first();

        $leavesData = [
            ['emp_idx' => 0, 'type' => $annuel,  'start' => '2026-07-01', 'end' => '2026-07-31', 'status' => 'approuvé',   'reason' => 'Vacances estivales'],
            ['emp_idx' => 5, 'type' => $annuel,  'start' => '2026-07-01', 'end' => '2026-07-31', 'status' => 'approuvé',   'reason' => 'Vacances estivales'],
            ['emp_idx' => 6, 'type' => $maladie, 'start' => '2026-06-10', 'end' => '2026-06-12', 'status' => 'approuvé',   'reason' => 'Congé maladie'],
            ['emp_idx' => 3, 'type' => $annuel,  'start' => '2026-08-04', 'end' => '2026-08-08', 'status' => 'en_attente', 'reason' => 'Congé prévu'],
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
                    'company_id'    => $cid,
                    'employee_id'   => $emp->id,
                    'leave_type_id' => $ld['type']->id,
                    'start_date'    => $ld['start'],
                    'end_date'      => $ld['end'],
                    'reason'        => $ld['reason'],
                    'status'        => $ld['status'],
                    'approved_by'   => in_array($ld['status'], ['approuvé', 'refusé']) ? $rh?->id : null,
                    'approved_at'   => in_array($ld['status'], ['approuvé', 'refusé']) ? now() : null,
                ]);
            }
        }

        $this->command->info(sprintf(
            'Demo : %d employés, présences (30j), congés.',
            count($employees)
        ));
    }
}
