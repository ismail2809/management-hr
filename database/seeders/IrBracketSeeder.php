<?php

namespace Database\Seeders;

use App\Models\IrBracket;
use Illuminate\Database\Seeder;

class IrBracketSeeder extends Seeder
{
    public function run(): void
    {
        IrBracket::truncate();

        // Barème IR Maroc 2024 — DGI (base annuelle en MAD)
        $brackets = [
            ['min_salary' => 0,      'max_salary' => 30000,   'rate_percentage' => 0,  'deduction_amount' => 0],
            ['min_salary' => 30001,  'max_salary' => 50000,   'rate_percentage' => 10, 'deduction_amount' => 3000],
            ['min_salary' => 50001,  'max_salary' => 60000,   'rate_percentage' => 20, 'deduction_amount' => 8000],
            ['min_salary' => 60001,  'max_salary' => 80000,   'rate_percentage' => 30, 'deduction_amount' => 14000],
            ['min_salary' => 80001,  'max_salary' => 180000,  'rate_percentage' => 34, 'deduction_amount' => 17200],
            ['min_salary' => 180001, 'max_salary' => null,    'rate_percentage' => 38, 'deduction_amount' => 24400],
        ];

        foreach ($brackets as $bracket) {
            IrBracket::create($bracket);
        }
    }
}
