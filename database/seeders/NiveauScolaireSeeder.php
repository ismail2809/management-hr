<?php

namespace Database\Seeders;

use App\Models\EcoleSettings;
use App\Models\NiveauScolaire;
use Illuminate\Database\Seeder;

class NiveauScolaireSeeder extends Seeder
{
    public function run(): void
    {
        $company = EcoleSettings::where('nom_ecole', 'Les écoles AL BARAIME')->first();

        if (! $company) {
            $this->command->warn('NiveauScolaireSeeder : company introuvable, seeder ignoré.');
            return;
        }

        $niveaux = [
            ['name' => 'Préscolaire', 'order' => 1],
            ['name' => 'Primaire',    'order' => 2],
            ['name' => 'Collège',     'order' => 3],
            ['name' => 'Lycée',       'order' => 4],
        ];

        foreach ($niveaux as $data) {
            NiveauScolaire::withoutGlobalScopes()->firstOrCreate(
                ['ecole_setting_id' => $company->id, 'name' => $data['name']],
                ['order' => $data['order']]
            );
        }

        $this->command->info('Niveaux scolaires seedés : ' . count($niveaux));
    }
}
