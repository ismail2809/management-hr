<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            ['code' => 'attestation_travail',  'name' => 'Attestation de travail',  'categorie' => 'document', 'sort_order' => 1],
            ['code' => 'attestation_salaire',  'name' => 'Attestation de salaire',  'categorie' => 'document', 'sort_order' => 2],
            ['code' => 'bulletin_paie',        'name' => 'Bulletin de paie',         'categorie' => 'document', 'sort_order' => 3],
            ['code' => 'attestation_ir',       'name' => 'Attestation IR',           'categorie' => 'document', 'sort_order' => 4],
            ['code' => 'credit_irrevocable',   'name' => 'Crédit irrévocable',       'categorie' => 'document', 'sort_order' => 5],
            ['code' => 'attestation_cnss',     'name' => 'Attestation CNSS',         'categorie' => 'document', 'sort_order' => 6],
            ['code' => 'ordre_mission',        'name' => 'Ordre de mission',         'categorie' => 'document', 'sort_order' => 7],
            ['code' => 'certificat_travail',   'name' => 'Certificat de travail',    'categorie' => 'document', 'sort_order' => 8],
            ['code' => 'materiel',             'name' => 'Matériel',                 'categorie' => 'autre',    'sort_order' => 1],
            ['code' => 'grande_salle',         'name' => 'Grande salle',             'categorie' => 'autre',    'sort_order' => 2],
            ['code' => 'photocopie',           'name' => 'Photocopie',               'categorie' => 'autre',    'sort_order' => 3],
            ['code' => 'rencontre_parents',    'name' => 'Rencontre parents',        'categorie' => 'autre',    'sort_order' => 4],
            ['code' => 'rencontre_direction',  'name' => 'Rencontre direction',      'categorie' => 'autre',    'sort_order' => 5],
            ['code' => 'formation',            'name' => 'Formation',                'categorie' => 'autre',    'sort_order' => 6],
            ['code' => 'activites',            'name' => 'Activités',                'categorie' => 'autre',    'sort_order' => 7],
            ['code' => 'divers',               'name' => 'Divers',                   'categorie' => 'autre',    'sort_order' => 8],
        ];

        foreach ($documents as $data) {
            DocumentType::firstOrCreate(['code' => $data['code']], array_merge($data, ['active' => true]));
        }
    }
}
