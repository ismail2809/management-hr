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
            'Préscolaire' => ['Petite section', 'Moyenne section', 'Grande section'],
            'Primaire'    => ['1ère année', '2ème année', '3ème année', '4ème année', '5ème année', '6ème année'],
            'Collège'     => ['1ère année collège', '2ème année collège', '3ème année collège'],
            'Lycée'       => ['Tronc commun', '1ère bac', '2ème bac'],
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
