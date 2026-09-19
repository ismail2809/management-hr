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
            EcoleSettingsSeeder::class,
            LeaveTypeSeeder::class,
            ProfessionSeeder::class,
            EmployeeSeeder::class,
            NiveauScolaireSeeder::class,
            GroupeSeeder::class,
            NatureDocumentSeeder::class,
        ]);

        // Générer les permissions Shield avant d'assigner les rôles
        $this->command->info('Generating Shield permissions...');
        // --option est obligatoire ici : sans lui, shield:generate ouvre un prompt
        // interactif dont l'affichage est avalé par le buffer d'Artisan::call()
        // et le seeder se fige en attendant une saisie invisible.
        Artisan::call('shield:generate', [
            '--all'            => true,
            '--panel'          => 'app',
            '--option'         => 'policies_and_permissions',
            '--no-interaction' => true,
        ]);
        $this->command->info(Artisan::output());

        $this->call([
            RolesPermissionsSeeder::class,
        ]);
    }
}
