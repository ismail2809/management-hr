<?php

namespace Database\Seeders;

use App\Models\CnssRate;
use Illuminate\Database\Seeder;

class CnssRateSeeder extends Seeder
{
    public function run(): void
    {
        // Taux globaux (company_id = null) — Maroc 2024
        CnssRate::whereNull('company_id')->delete();

        $rates = [
            // Salarié
            ['type' => 'employee', 'label' => 'CNSS',      'rate_percentage' => 4.48,  'plafond' => 6000.00],
            ['type' => 'employee', 'label' => 'AMO',       'rate_percentage' => 2.26,  'plafond' => null],
            // Patronal
            ['type' => 'employer', 'label' => 'CNSS',      'rate_percentage' => 10.77, 'plafond' => 6000.00],
            ['type' => 'employer', 'label' => 'AMO',       'rate_percentage' => 4.11,  'plafond' => null],
            ['type' => 'employer', 'label' => 'Formation', 'rate_percentage' => 1.60,  'plafond' => null],
        ];

        foreach ($rates as $rate) {
            CnssRate::create(array_merge($rate, ['company_id' => null]));
        }
    }
}
