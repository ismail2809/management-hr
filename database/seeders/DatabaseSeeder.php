<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            IrBracketSeeder::class,
            CnssRateSeeder::class,
            DocumentTypeSeeder::class,
            SuperAdminSeeder::class,
            TestCompanySeeder::class,
            DemoDataSeeder::class,
        ]);

        // Générer les permissions Shield avant d'assigner les rôles
        $this->command->info('Generating Shield permissions...');
        Artisan::call('shield:generate', ['--all' => true, '--panel' => 'app']);
        $this->command->info(Artisan::output());

        $this->call([
            RolesPermissionsSeeder::class,
        ]);
    }
}
