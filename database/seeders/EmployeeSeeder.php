<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Profession;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('name', 'Les écoles AL BARAIME')->first();

        if (! $company) {
            $this->command->warn('EmployeeSeeder : company introuvable, seeder ignoré.');
            return;
        }

        $getProfession = fn(string $name) => Profession::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('name', $name)
            ->first()?->id;

        $employees = [
            [
                'first_name'         => 'Fatima',
                'last_name'          => 'BENNANI',
                'gender'             => 'F',
                'cin'                => 'BE123456',
                'cnss_number'        => '1234567',
                'profession_id'      => $getProfession('Enseignante'),
                'contract_type'      => 'CDI',
                'marital_status'     => 'married',
                'number_of_children' => 2,
                'hire_date'          => '2018-09-01',
                'birth_date'         => '1985-03-15',
                'birth_place'        => 'Casablanca',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
            [
                'first_name'         => 'Youssef',
                'last_name'          => 'ALAMI',
                'gender'             => 'M',
                'cin'                => 'BE234567',
                'cnss_number'        => '2345678',
                'profession_id'      => $getProfession('Enseignant'),
                'contract_type'      => 'CDI',
                'marital_status'     => 'married',
                'number_of_children' => 1,
                'hire_date'          => '2019-09-01',
                'birth_date'         => '1988-07-22',
                'birth_place'        => 'Rabat',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
            [
                'first_name'         => 'Khadija',
                'last_name'          => 'OUALI',
                'gender'             => 'F',
                'cin'                => 'BE345678',
                'cnss_number'        => '3456789',
                'profession_id'      => $getProfession('Enseignante'),
                'contract_type'      => 'CDD',
                'marital_status'     => 'single',
                'number_of_children' => 0,
                'hire_date'          => '2022-09-01',
                'birth_date'         => '1995-11-08',
                'birth_place'        => 'Fès',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
            [
                'first_name'         => 'Mohamed',
                'last_name'          => 'TAHIRI',
                'gender'             => 'M',
                'cin'                => 'BE456789',
                'cnss_number'        => '4567890',
                'profession_id'      => $getProfession('Directeur'),
                'contract_type'      => 'CDI',
                'marital_status'     => 'married',
                'number_of_children' => 3,
                'hire_date'          => '2015-09-01',
                'birth_date'         => '1975-01-30',
                'birth_place'        => 'Marrakech',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
            [
                'first_name'         => 'Zineb',
                'last_name'          => 'CHAKIR',
                'gender'             => 'F',
                'cin'                => 'BE567890',
                'cnss_number'        => '5678901',
                'profession_id'      => $getProfession('Secrétaire'),
                'contract_type'      => 'CDI',
                'marital_status'     => 'single',
                'number_of_children' => 0,
                'hire_date'          => '2020-01-15',
                'birth_date'         => '1992-06-18',
                'birth_place'        => 'Casablanca',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
            [
                'first_name'         => 'Hassan',
                'last_name'          => 'MRABET',
                'gender'             => 'M',
                'cin'                => 'BE678901',
                'cnss_number'        => '6789012',
                'profession_id'      => $getProfession('Chauffeur'),
                'contract_type'      => 'CDI',
                'marital_status'     => 'married',
                'number_of_children' => 2,
                'hire_date'          => '2017-03-01',
                'birth_date'         => '1980-09-05',
                'birth_place'        => 'Salé',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
            [
                'first_name'         => 'Aicha',
                'last_name'          => 'ZIANI',
                'gender'             => 'F',
                'cin'                => 'BE789012',
                'cnss_number'        => '7890123',
                'profession_id'      => $getProfession('Femme de ménage'),
                'contract_type'      => 'CDI',
                'marital_status'     => 'divorced',
                'number_of_children' => 1,
                'hire_date'          => '2016-06-01',
                'birth_date'         => '1978-12-20',
                'birth_place'        => 'Casablanca',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
            [
                'first_name'         => 'Rachid',
                'last_name'          => 'BOUDALI',
                'gender'             => 'M',
                'cin'                => 'BE890123',
                'cnss_number'        => '8901234',
                'profession_id'      => $getProfession('Gardien'),
                'contract_type'      => 'CDI',
                'marital_status'     => 'married',
                'number_of_children' => 4,
                'hire_date'          => '2014-01-01',
                'birth_date'         => '1972-04-14',
                'birth_place'        => 'Meknès',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
            [
                'first_name'         => 'Nadia',
                'last_name'          => 'SENHAJI',
                'gender'             => 'F',
                'cin'                => 'BE901234',
                'cnss_number'        => '9012345',
                'profession_id'      => $getProfession('Assistante de transport'),
                'contract_type'      => 'CDD',
                'marital_status'     => 'single',
                'number_of_children' => 0,
                'hire_date'          => '2023-09-01',
                'birth_date'         => '1998-02-27',
                'birth_place'        => 'Agadir',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
            [
                'first_name'         => 'Omar',
                'last_name'          => 'KARIMI',
                'gender'             => 'M',
                'cin'                => 'BE012345',
                'cnss_number'        => '0123456',
                'profession_id'      => $getProfession('Surveillant général'),
                'contract_type'      => 'CDI',
                'marital_status'     => 'married',
                'number_of_children' => 2,
                'hire_date'          => '2021-09-01',
                'birth_date'         => '1983-08-11',
                'birth_place'        => 'Tanger',
                'nationality'        => 'Marocaine',
                'city'               => 'Casablanca',
                'status'             => 'active',
            ],
        ];

        $count = 0;
        foreach ($employees as $index => $data) {
            $matricule = 'EMP-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Employee::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'cin' => $data['cin']],
                array_merge($data, [
                    'company_id' => $company->id,
                    'matricule'  => $matricule,
                ])
            );
            $count++;
        }

        $this->command->info("Employés seedés : {$count}");
    }
}
