<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Position;
use Illuminate\Database\Seeder;

class SchoolPositionsSeeder extends Seeder
{
    public function run(): void
    {
        // Applique les postes à toutes les companies (ou passer un company_id en argument)
        $companies = Company::all();

        $postes = [
            // ── Enseignement ────────────────────────────────────────────────
            ['category' => 'Enseignement', 'title' => 'Enseignant(e)',                  'base_salary' => 4500],
            ['category' => 'Enseignement', 'title' => 'Enseignant(e) Préscolaire',       'base_salary' => 4000],
            ['category' => 'Enseignement', 'title' => 'Enseignant(e) Primaire',          'base_salary' => 4500],
            ['category' => 'Enseignement', 'title' => 'Enseignant(e) Collège',           'base_salary' => 5000],
            ['category' => 'Enseignement', 'title' => 'Enseignant(e) Lycée',             'base_salary' => 5500],
            ['category' => 'Enseignement', 'title' => 'Vacataire',                       'base_salary' => 2500],
            ['category' => 'Enseignement', 'title' => 'Vacataire Langue',                'base_salary' => 2800],
            ['category' => 'Enseignement', 'title' => 'Coordinateur(rice) Pédagogique', 'base_salary' => 7000],
            ['category' => 'Enseignement', 'title' => 'Conseiller(ère) Pédagogique',    'base_salary' => 6500],
            ['category' => 'Enseignement', 'title' => 'Psychologue Scolaire',            'base_salary' => 6000],

            // ── Administration ───────────────────────────────────────────────
            ['category' => 'Administration', 'title' => 'Directeur(rice) École 1',       'base_salary' => 12000],
            ['category' => 'Administration', 'title' => 'Directeur(rice) École 2',       'base_salary' => 12000],
            ['category' => 'Administration', 'title' => 'Directeur(rice) École 3',       'base_salary' => 12000],
            ['category' => 'Administration', 'title' => 'Directeur(rice) Général(e)',    'base_salary' => 18000],
            ['category' => 'Administration', 'title' => 'Directeur(rice) Pédagogique',  'base_salary' => 15000],
            ['category' => 'Administration', 'title' => 'Directeur(rice) Adjoint(e)',   'base_salary' => 9000],
            ['category' => 'Administration', 'title' => 'Responsable RH',               'base_salary' => 8000],
            ['category' => 'Administration', 'title' => 'Comptable',                    'base_salary' => 7000],
            ['category' => 'Administration', 'title' => 'Secrétaire',                   'base_salary' => 4500],
            ['category' => 'Administration', 'title' => 'Secrétaire de Direction',      'base_salary' => 5500],
            ['category' => 'Administration', 'title' => 'Chargé(e) de Communication',  'base_salary' => 6000],
            ['category' => 'Administration', 'title' => 'Responsable Informatique',     'base_salary' => 7500],

            // ── Support ──────────────────────────────────────────────────────
            ['category' => 'Support', 'title' => 'Femme de Ménage',                     'base_salary' => 2900],
            ['category' => 'Support', 'title' => 'Agent d\'Entretien',                  'base_salary' => 2900],
            ['category' => 'Support', 'title' => 'Agent de Sécurité',                   'base_salary' => 3200],
            ['category' => 'Support', 'title' => 'Gardien(ne)',                          'base_salary' => 3000],
            ['category' => 'Support', 'title' => 'Cuisinier(ère) / Cantine',            'base_salary' => 3500],
            ['category' => 'Support', 'title' => 'Agent de Bibliothèque',               'base_salary' => 3500],
            ['category' => 'Support', 'title' => 'Infirmier(ère) Scolaire',             'base_salary' => 5000],
            ['category' => 'Support', 'title' => 'Technicien(ne) Maintenance',          'base_salary' => 4000],

            // ── Transport ────────────────────────────────────────────────────
            ['category' => 'Transport', 'title' => 'Chauffeur de Bus',                  'base_salary' => 4000],
            ['category' => 'Transport', 'title' => 'Chauffeur de Minibus',               'base_salary' => 3800],
            ['category' => 'Transport', 'title' => 'Aide-Chauffeur',                     'base_salary' => 2900],
            ['category' => 'Transport', 'title' => 'Responsable Transport',              'base_salary' => 6000],
        ];

        foreach ($companies as $company) {
            foreach ($postes as $poste) {
                Position::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'title'      => $poste['title'],
                    ],
                    [
                        'category'    => $poste['category'],
                        'base_salary' => $poste['base_salary'],
                    ]
                );
            }
        }

        $this->command->info(count($postes) . ' postes créés par company (' . $companies->count() . ' company/ies).');
    }
}
