<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TestCompanySeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles RH
        foreach (['admin', 'rh', 'manager', 'comptable'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Company de test
        $company = Company::firstOrCreate(
            ['name' => 'Les Écoles Al Baraime'],
            [
                'ice'              => '001234567000012',
                'rc'               => 'RC-123456',
                'patente'          => 'PAT-789',
                'cnss_affiliation' => 'CNSS-00123',
                'city'             => 'Casablanca',
                'email'            => 'contact@albaraime.ma',
                'phone'            => '+212522000000',
            ]
        );

        // User admin de la company
        $adminUser = User::updateOrCreate(
            ['email' => 'rh@societe-test.ma'],
            [
                'name'       => 'Responsable RH',
                'password'   => bcrypt('password'),
                'company_id' => $company->id,
            ]
        );
        $adminUser->syncRoles(['rh']);

        // User manager
        $manager = User::updateOrCreate(
            ['email' => 'manager@societe-test.ma'],
            [
                'name'       => 'Manager Test',
                'password'   => bcrypt('password'),
                'company_id' => $company->id,
            ]
        );
        $manager->syncRoles(['manager']);

        // User employé de test
        $employe = User::updateOrCreate(
            ['email' => 'employe@test.com'],
            [
                'name'       => 'Employé Test',
                'password'   => bcrypt('password'),
                'company_id' => $company->id,
            ]
        );
        $employe->syncRoles(['rh']);

        // Types de congé par défaut
        $leaveTypes = [
            ['name' => 'Congé annuel',  'legal_days_per_year' => 18],
            ['name' => 'Congé maladie', 'legal_days_per_year' => 180],
            ['name' => 'Congé maternité', 'legal_days_per_year' => 98],
            ['name' => 'Sans solde',    'legal_days_per_year' => 0],
        ];

        // withoutGlobalScopes pour éviter le CompanyScope sans auth
        foreach ($leaveTypes as $lt) {
            LeaveType::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'name' => $lt['name']],
                ['legal_days_per_year' => $lt['legal_days_per_year']]
            );
        }
    }
}
