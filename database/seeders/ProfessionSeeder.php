<?php

namespace Database\Seeders;

use App\Models\EcoleSettings;
use App\Models\Profession;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $company = EcoleSettings::where('nom_ecole', 'Les écoles AL BARAIME')->first();

        if (! $company) {
            $this->command->warn('ProfessionSeeder : company introuvable, seeder ignoré.');
            return;
        }

        $professions = [
            'Enseignante',
            'Enseignant',
            'Femme de ménage',
            'Directeur',
            'Chauffeur',
            'Gardien',
            'Assistante de transport',
            'Surveillant général',
            'Secrétaire',
        ];

        foreach ($professions as $name) {
            Profession::withoutGlobalScopes()->firstOrCreate(
                ['ecole_setting_id' => $company->id, 'name' => $name]
            );
        }

        $this->command->info('Professions seedées : ' . count($professions));
    }
}
