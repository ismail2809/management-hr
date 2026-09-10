<?php

namespace Database\Seeders;

use App\Models\EcoleSettings;
use App\Models\NatureDocument;
use Illuminate\Database\Seeder;

class NatureDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $company = EcoleSettings::where('nom_ecole', 'Les écoles AL BARAIME')->first();

        if (! $company) {
            $this->command->warn('NatureDocumentSeeder : company introuvable, seeder ignoré.');
            return;
        }

        $natures = [
            ['name' => 'Examen',              'sort_order' => 1],
            ['name' => "Série d'exercices",   'sort_order' => 2],
            ['name' => 'Contrôle continu',    'sort_order' => 3],
            ['name' => "Contrôle d'essai",    'sort_order' => 4],
        ];

        foreach ($natures as $data) {
            NatureDocument::withoutGlobalScopes()->firstOrCreate(
                ['ecole_setting_id' => $company->id, 'name' => $data['name']],
                ['sort_order' => $data['sort_order'], 'active' => true]
            );
        }

        $this->command->info('Natures de document seedées : ' . count($natures));
    }
}
