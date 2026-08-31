<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Groupe;
use App\Models\NiveauScolaire;
use Illuminate\Database\Seeder;

class GroupeSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('name', 'Les Écoles Al Baraime')->first();

        if (! $company) {
            $this->command->warn('GroupeSeeder : company introuvable, seeder ignoré.');
            return;
        }

        $data = [
            'Primaire' => [
                '1ère Année du primaire',
                '2ème Année du primaire',
                '3ème Année du primaire',
                '4ème Année du primaire',
                '5ème Année du primaire',
                '6ème Année du primaire',
            ],
            'Collège' => [
                '1ère Année du collège – Parcours International',
                '2ème Année du collège – Parcours International',
                '3ème Année du collège – Parcours International',
            ],
            'Lycée' => [
                'Tronc Commun Scientifiques – Parcours International',
                '1ère Année du Baccalauréat Sciences Expérimentales – Parcours International',
                '1ère Année du Baccalauréat Sciences Mathématiques',
                '2ème Année du Baccalauréat Sciences Physiques – Parcours International',
            ],
        ];

        $count = 0;

        foreach ($data as $niveauName => $groupes) {
            $niveau = NiveauScolaire::withoutGlobalScopes()
                ->where('company_id', $company->id)
                ->where('name', $niveauName)
                ->first();

            if (! $niveau) {
                $this->command->warn("Niveau « {$niveauName} » introuvable — groupes ignorés.");
                continue;
            }

            foreach ($groupes as $groupeName) {
                Groupe::withoutGlobalScopes()->firstOrCreate(
                    ['company_id' => $company->id, 'niveau_scolaire_id' => $niveau->id, 'name' => $groupeName]
                );
                $count++;
            }
        }

        $this->command->info("Groupes seedés : {$count}");
    }
}
