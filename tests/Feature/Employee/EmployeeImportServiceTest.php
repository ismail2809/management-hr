<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\Profession;
use App\Services\EmployeeImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmployeeImportServiceTest extends TestCase
{
    use RefreshDatabase;

    private array $fichiers = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->defaultEcole();
    }

    protected function tearDown(): void
    {
        foreach ($this->fichiers as $f) {
            @unlink($f);
        }

        parent::tearDown();
    }

    /** Construit un vrai .xlsx à partir d'un tableau de lignes. */
    private function xlsx(array $lignes): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        foreach ($lignes as $i => $ligne) {
            foreach (array_values($ligne) as $j => $valeur) {
                $sheet->setCellValue([$j + 1, $i + 1], $valeur);
            }
        }

        $chemin = tempnam(sys_get_temp_dir(), 'import_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($chemin);

        $this->fichiers[] = $chemin;

        return $chemin;
    }

    private function importer(array $lignes): array
    {
        return (new EmployeeImportService())->import($this->xlsx($lignes), $this->defaultEcole()->id);
    }

    #[Test]
    public function un_fichier_nominal_cree_les_employes(): void
    {
        $resultat = $this->importer([
            ['Matricule', 'Nom', 'Prénom', 'CIN', 'Sexe'],
            ['M001', 'ALAMI', 'Salma', 'AB1234', 'F'],
            ['M002', 'TAZI', 'Karim', 'CD5678', 'M'],
        ]);

        $this->assertSame(2, $resultat['imported']);
        $this->assertSame([], $resultat['errors']);

        $this->assertDatabaseHas('employees', ['last_name' => 'ALAMI', 'first_name' => 'Salma', 'gender' => 'F']);
        $this->assertDatabaseHas('employees', ['last_name' => 'TAZI', 'cin' => 'CD5678']);
    }

    #[Test]
    public function tous_les_employes_sont_rattaches_a_lecole_ciblee(): void
    {
        $this->importer([
            ['Matricule', 'Nom', 'Prénom'],
            ['M001', 'ALAMI', 'Salma'],
        ]);

        $this->assertSame(
            $this->defaultEcole()->id,
            Employee::withoutGlobalScopes()->first()->ecole_setting_id,
        );
    }

    #[Test]
    public function un_employe_existant_est_mis_a_jour_via_son_cin(): void
    {
        $existant = $this->employee(['cin' => 'AB1234', 'first_name' => 'Ancien', 'last_name' => 'NOM']);

        $resultat = $this->importer([
            ['Nom', 'Prénom', 'CIN'],
            ['NOUVEAU', 'Prenom', 'AB1234'],
        ]);

        $this->assertSame(1, $resultat['imported']);
        $this->assertSame(1, Employee::withoutGlobalScopes()->count(), 'Aucun doublon ne doit être créé.');

        $existant->refresh();
        $this->assertSame('NOUVEAU', $existant->last_name);
    }

    #[Test]
    public function le_matricule_existant_nest_jamais_ecrase(): void
    {
        $existant = $this->employee(['cin' => 'AB1234', 'matricule' => 'ORIGINAL']);

        $this->importer([
            ['Matricule', 'Nom', 'CIN'],
            ['REMPLACE', 'ALAMI', 'AB1234'],
        ]);

        $this->assertSame('ORIGINAL', $existant->fresh()->matricule, 'matricule est un champ protégé.');
    }

    #[Test]
    public function la_profession_est_creee_a_la_volee_et_rattachee(): void
    {
        $this->importer([
            ['Nom', 'Prénom', 'Profession'],
            ['ALAMI', 'Salma', 'Enseignante'],
        ]);

        $profession = Profession::withoutGlobalScopes()->where('name', 'Enseignante')->first();

        $this->assertNotNull($profession);
        $this->assertSame($this->defaultEcole()->id, $profession->ecole_setting_id);
        $this->assertSame($profession->id, Employee::withoutGlobalScopes()->first()->profession_id);
    }

    #[Test]
    public function les_dates_au_format_francais_sont_converties(): void
    {
        $this->importer([
            ['Nom', 'Prénom', 'Date naissance'],
            ['ALAMI', 'Salma', '15/03/1990'],
        ]);

        $this->assertSame(
            '1990-03-15',
            Employee::withoutGlobalScopes()->first()->birth_date->format('Y-m-d'),
        );
    }

    #[Test]
    public function les_lignes_sans_nom_sont_ignorees(): void
    {
        // La ligne parasite doit contenir au moins une cellule non vide,
        // sinon PhpSpreadsheet ne la matérialise pas du tout dans le fichier.
        $resultat = $this->importer([
            ['Matricule', 'Nom', 'Prénom'],
            ['M001', 'ALAMI', 'Salma'],
            ['M002', '', ''],
        ]);

        $this->assertSame(1, $resultat['imported']);
        $this->assertSame(1, $resultat['skipped']);
        $this->assertSame(1, Employee::withoutGlobalScopes()->count());
    }

    #[Test]
    public function les_lignes_de_titre_ou_de_total_sont_ignorees(): void
    {
        $resultat = $this->importer([
            ['Matricule', 'Nom', 'Prénom'],
            ['M001', 'ALAMI', 'Salma'],
            ['', 'TOTAL GENERAL', ''],
        ]);

        $this->assertSame(1, $resultat['imported']);
        $this->assertSame(1, $resultat['skipped']);
    }

    #[Test]
    public function le_sexe_hors_M_ou_F_est_neutralise(): void
    {
        $this->importer([
            ['Nom', 'Prénom', 'Sexe'],
            ['ALAMI', 'Salma', 'Inconnu'],
        ]);

        $this->assertNull(Employee::withoutGlobalScopes()->first()->gender);
    }

    #[Test]
    public function le_statut_par_defaut_est_actif(): void
    {
        $this->importer([
            ['Nom', 'Prénom'],
            ['ALAMI', 'Salma'],
        ]);

        $this->assertSame('actif', Employee::withoutGlobalScopes()->first()->status);
    }

    #[Test]
    public function les_entetes_sont_reconnues_quelle_que_soit_la_casse_et_les_accents(): void
    {
        $this->importer([
            ['NOM', 'PRENOM', 'Téléphone'],
            ['ALAMI', 'Salma', '0600000000'],
        ]);

        $employe = Employee::withoutGlobalScopes()->first();

        $this->assertSame('ALAMI', $employe->last_name);
        $this->assertSame('Salma', $employe->first_name);
        $this->assertSame('0600000000', $employe->phone);
    }
}
