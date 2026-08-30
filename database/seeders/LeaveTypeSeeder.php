<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('name', 'Les Écoles Al Baraime')->first();

        if (! $company) {
            $this->command->warn('LeaveTypeSeeder : company introuvable, seeder ignoré.');
            return;
        }

        $types = [
            'Congé maladie',
            'Congé maternité',
        ];

        foreach ($types as $name) {
            LeaveType::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'name' => $name]
            );
        }

        $this->command->info('Types de congé seedés : ' . count($types));
    }
}
