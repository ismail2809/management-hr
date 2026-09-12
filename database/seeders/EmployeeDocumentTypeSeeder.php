<?php

namespace Database\Seeders;

use App\Models\EmployeeDocumentType;
use Illuminate\Database\Seeder;

class EmployeeDocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'photo',              'name' => 'Photo',                'sort_order' => 1],
            ['code' => 'cin',                'name' => 'CIN (scan)',           'sort_order' => 2],
            ['code' => 'extrait_naissance',  'name' => 'Extrait de naissance', 'sort_order' => 3],
            ['code' => 'carte_cnss',         'name' => 'Carte CNSS',           'sort_order' => 4],
            ['code' => 'rib',                'name' => 'RIB (scan)',           'sort_order' => 5],
            ['code' => 'diplome',            'name' => 'Diplôme',              'sort_order' => 6],
            ['code' => 'contrat_anapec',     'name' => 'Contrat ANAPEC',       'sort_order' => 7],
            ['code' => 'autre',              'name' => 'Autre',                'sort_order' => 8],
        ];

        foreach ($types as $data) {
            EmployeeDocumentType::firstOrCreate(
                ['code' => $data['code']],
                array_merge($data, ['active' => true, 'ecole_setting_id' => null])
            );
        }
    }
}
