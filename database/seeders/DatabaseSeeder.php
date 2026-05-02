<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            IrBracketSeeder::class,
            CnssRateSeeder::class,
            SuperAdminSeeder::class,
            TestCompanySeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
