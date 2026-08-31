<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DocumentTypeSeeder::class,
            CommunicationMethodSeeder::class,
            SuperAdminSeeder::class,
            TestCompanySeeder::class,
            LeaveTypeSeeder::class,
            ProfessionSeeder::class,
            NiveauScolaireSeeder::class,
            GroupeSeeder::class,
            NatureDocumentSeeder::class,
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
