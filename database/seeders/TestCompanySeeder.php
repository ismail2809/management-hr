<?php

namespace Database\Seeders;

use App\Models\Company;
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
            ['name' => 'Les écoles AL BARAIME'],
            [
                'ice'              => '000503057000080',
                // 'rc'               => 'RC-123456',
                // 'patente'          => 'PAT-789',
                // 'cnss_affiliation' => 'CNSS-00123',
                'city'             => 'Casablanca',
                'email'            => 'albaraime.viescholaire@gmail.com',
                'phone'            => '+212523358346',
            ]
        );

    }
}
