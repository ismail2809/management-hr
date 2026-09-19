<?php

namespace Tests\Feature\Bugs;

use App\Filament\Admin\Resources\AutreDemandeResource;
use App\Filament\Admin\Resources\EmployeeResource;
use App\Filament\Admin\Resources\EmployeeResource\RelationManagers\GroupesRelationManager;
use App\Filament\Admin\Resources\UserResource;
use App\Models\DocumentRequest;
use App\Models\Employee;
use App\Models\Leave;
use App\Services\EmployeeImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ⚠️ Ces tests ÉCHOUENT tant que les anomalies ne sont pas corrigées.
 * Anomalies fonctionnelles (sans erreur 500) — voir BUGS.md.
 */
#[Group('bugs')]
class BehaviourRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    /**
     * BUG-01 — Les gardes Resource::canDelete...() ne sont jamais appelées par
     * Filament v4 (0 occurrence dans vendor/). C'est la policy qui tranche, et
     * RolesPermissionsSeeder accorde les permissions « Delete » et
     * « DeleteAny » au directeur.
     * Correctif : retirer ces permissions au directeur dans le seeder.
     */
    #[Test]
    public function bug01_le_directeur_ne_doit_pas_pouvoir_supprimer_un_employe(): void
    {
        $employe = $this->employee();
        $user    = $this->actingAsRole('directeur');

        $this->assertFalse(
            $user->can('delete', $employe),
            'La suppression est censée être réservée au super-admin.',
        );
        $this->assertFalse($user->can('DeleteAny:Employee'));
    }

    #[Test]
    public function bug01_le_directeur_ne_doit_pas_pouvoir_supprimer_un_utilisateur(): void
    {
        $user = $this->actingAsRole('directeur');

        $this->assertFalse($user->can('DeleteAny:User'));
    }

    /**
     * BUG-02 — La profession « Professeur » n'existe pas (le seeder crée
     * « Enseignant » / « Enseignante »), mais trois emplacements comparent à
     * cette chaîne littérale. Correctif : utiliser Employee::isProfesseur().
     */
    #[Test]
    public function bug02_longlet_groupes_doit_apparaitre_pour_un_enseignant(): void
    {
        $this->actingAsRole('directeur');

        $enseignant = $this->employee(['profession_id' => $this->profession('Enseignante')->id]);

        $this->assertTrue(
            GroupesRelationManager::canViewForRecord($enseignant->fresh(), EmployeeResource\Pages\ViewEmployee::class),
            "L'onglet « Groupes affectés » doit être visible pour une Enseignante.",
        );
    }

    /**
     * BUG-05 — HrStatsOverview compte les congés sans filtrer sur la
     * catégorie : la tuile « Congés en attente » inclut les absences.
     */
    #[Test]
    public function bug05_la_tuile_conges_ne_doit_pas_compter_les_absences(): void
    {
        $this->actingAsRole('secretaire');
        $employe = $this->employee();

        foreach (['conge', 'absence'] as $categorie) {
            Leave::create([
                'employee_id' => $employe->id,
                'categorie'   => $categorie,
                'start_date'  => '2026-09-14',
                'end_date'    => '2026-09-18',
                'status'      => 'en_attente',
            ]);
        }

        // Reproduit HrStatsOverview.php:28
        $congesEnAttente = Leave::where('status', 'en_attente')->count();

        $this->assertSame(1, $congesEnAttente, 'Un seul congé est en attente, pas deux.');
    }

    // BUG-07 : FAUX POSITIF. Laravel regroupe les clauses préexistantes entre
    // parenthèses au moment d'appliquer un global scope, le OR ne casse donc
    // pas l'isolation. La preuve vit dans CompanyScopeTest, suite principale.

    /**
     * BUG-08 — Le badge fait User::count() (non filtré) alors que le tableau
     * filtre sur ecole_setting_id. Correctif : réutiliser getEloquentQuery().
     */
    #[Test]
    public function bug08_le_badge_utilisateurs_doit_correspondre_au_tableau(): void
    {
        $this->userWithRole('super-admin');            // ecole_setting_id = NULL
        $this->actingAsRole('directeur');              // école par défaut

        $this->assertSame(
            (string) UserResource::getEloquentQuery()->count(),
            UserResource::getNavigationBadge(),
        );
    }

    /**
     * BUG-10 — ViewEmployee::getLeaveStats() utilise diffInDays alors que le
     * modèle utilise diffInWeekdays. Correctif : utiliser $l->duration_days.
     */
    #[Test]
    public function bug10_les_deux_calculs_de_duree_doivent_concorder(): void
    {
        $this->actingAsRole('directeur');
        $employe = $this->employee();

        $leave = Leave::create([
            'employee_id' => $employe->id,
            'categorie'   => 'conge',
            'start_date'  => '2026-09-18',   // vendredi
            'end_date'    => '2026-09-21',   // lundi
            'status'      => 'approuvé',
        ]);

        $profil = $leave->start_date->diffInDays($leave->end_date) + 1;   // ViewEmployee.php:96

        $this->assertSame(
            $leave->duration_days,
            $profil,
            'La fiche employé et la liste des congés doivent afficher la même durée.',
        );
    }

    /**
     * BUG-16 — isTitleRow() ignore toute ligne dont le nom CONTIENT « nom ».
     */
    #[Test]
    public function bug16_un_patronyme_contenant_nom_ne_doit_pas_etre_ignore(): void
    {
        $this->defaultEcole();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        foreach ([['Matricule', 'Nom', 'Prénom'], ['M001', 'BENOMAR', 'Youssef']] as $i => $ligne) {
            foreach (array_values($ligne) as $j => $v) {
                $sheet->setCellValue([$j + 1, $i + 1], $v);
            }
        }
        $chemin = tempnam(sys_get_temp_dir(), 'imp_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($chemin);

        try {
            (new EmployeeImportService())->import($chemin, $this->defaultEcole()->id);

            $this->assertDatabaseHas('employees', ['last_name' => 'BENOMAR']);
        } finally {
            @unlink($chemin);
        }
    }

    /**
     * BUG-17 — parseCnss() fait (string)(int)$raw : les zéros de tête sautent.
     */
    #[Test]
    public function bug17_un_numero_cnss_a_zero_initial_doit_etre_preserve(): void
    {
        $this->defaultEcole();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        foreach ([['Nom', 'Prénom', 'CNSS'], ['ALAMI', 'Salma', '0123456']] as $i => $ligne) {
            foreach (array_values($ligne) as $j => $v) {
                $sheet->setCellValueExplicit(
                    [$j + 1, $i + 1],
                    $v,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING,
                );
            }
        }
        $chemin = tempnam(sys_get_temp_dir(), 'imp_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($chemin);

        try {
            (new EmployeeImportService())->import($chemin, $this->defaultEcole()->id);

            $this->assertSame('0123456', Employee::withoutGlobalScopes()->first()->cnss_number);
        } finally {
            @unlink($chemin);
        }
    }

    /**
     * BUG-22 — ViewAutreDemande conditionne son bouton à fichier_final, mais
     * le formulaire d'AutreDemandeResource ne propose pas ce champ.
     */
    #[Test]
    public function bug22_le_formulaire_autre_demande_doit_exposer_fichier_final(): void
    {
        $this->actingAsRole('directeur');

        $source = file_get_contents(app_path('Filament/Admin/Resources/AutreDemandeResource.php'));

        $this->assertStringContainsString(
            "FileUpload::make('fichier_final')",
            $source,
            'Sans ce champ, le bouton « Télécharger document » ne peut jamais apparaître.',
        );
    }

    /**
     * BUG-13 — La resource legacy reste routable hors menu et contourne les
     * garde-fous des deux resources scindées.
     */
    #[Test]
    public function bug13_la_resource_legacy_ne_doit_plus_etre_routable(): void
    {
        $this->actingAsRole('surveillante');

        $this->get('/admin/document-requests')->assertNotFound();
    }

    /**
     * BUG-21 — La colonne affiche photocopie_date_souhaitee en repli mais
     * trie sur date_souhaitee uniquement.
     */
    #[Test]
    public function bug21_le_tri_date_souhaitee_doit_couvrir_les_photocopies(): void
    {
        $this->actingAsRole('surveillante');
        $employe = $this->employee();

        DocumentRequest::create([
            'employee_id'               => $employe->id,
            'categorie'                 => 'autre',
            'type'                      => 'photocopie',
            'format'                    => 'digital',
            'status'                    => 'en_attente',
            'photocopie_date_souhaitee' => '2026-10-01',
        ]);

        $ligne = AutreDemandeResource::getEloquentQuery()
            ->orderBy('date_souhaitee')
            ->first();

        $this->assertNotNull(
            $ligne->date_souhaitee,
            'Le tri porte sur une colonne vide pour les photocopies.',
        );
    }
}
