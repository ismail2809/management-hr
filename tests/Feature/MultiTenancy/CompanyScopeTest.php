<?php

namespace Tests\Feature\MultiTenancy;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Isolation multi-écoles assurée par CompanyScope / HasCompanyScope
 * (colonne ecole_setting_id NOT NULL).
 */
class CompanyScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    #[Test]
    public function un_utilisateur_ne_voit_que_les_employes_de_son_ecole(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $ecoleB = $this->ecole(['nom_ecole' => 'B']);

        $this->employee(['first_name' => 'Alice'], $ecoleA);
        $this->employee(['first_name' => 'Bob'], $ecoleB);

        $this->actingAsRole('directeur', $ecoleA);

        $this->assertCount(1, Employee::all());
        $this->assertSame('Alice', Employee::first()->first_name);
    }

    #[Test]
    public function un_utilisateur_ne_peut_pas_charger_un_employe_dune_autre_ecole_par_id(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $ecoleB = $this->ecole(['nom_ecole' => 'B']);

        $employeA = $this->employee([], $ecoleA);

        $this->actingAsRole('directeur', $ecoleB);

        $this->assertNull(
            Employee::find($employeA->id),
            'CompanyScope doit empêcher tout accès inter-écoles par identifiant.',
        );
    }

    #[Test]
    public function le_ecole_setting_id_est_renseigne_automatiquement_a_la_creation(): void
    {
        $ecole = $this->ecole();
        $this->actingAsRole('directeur', $ecole);

        $type = LeaveType::create(['name' => 'Congé annuel']);

        $this->assertSame($ecole->id, $type->ecole_setting_id);
    }

    #[Test]
    public function le_super_admin_voit_toutes_les_ecoles(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $ecoleB = $this->ecole(['nom_ecole' => 'B']);

        $this->employee([], $ecoleA);
        $this->employee([], $ecoleB);

        $this->actingAsRole('super-admin');

        $this->assertCount(2, Employee::all(), 'CompanyScope est court-circuité pour super-admin.');
    }

    #[Test]
    public function le_scope_sapplique_aussi_aux_conges(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $ecoleB = $this->ecole(['nom_ecole' => 'B']);

        foreach ([$ecoleA, $ecoleB] as $ecole) {
            Leave::withoutGlobalScopes()->create([
                'ecole_setting_id' => $ecole->id,
                'employee_id'      => $this->employee([], $ecole)->id,
                'categorie'        => 'conge',
                'start_date'       => '2026-01-05',
                'end_date'         => '2026-01-09',
                'status'           => 'en_attente',
            ]);
        }

        $this->actingAsRole('directeur', $ecoleA);

        $this->assertCount(1, Leave::all());
        $this->assertSame($ecoleA->id, Leave::first()->ecole_setting_id);
    }

    /**
     * Une clause `orWhere` écrite avant l'application du scope ne peut pas
     * casser l'isolation : Laravel regroupe les conditions préexistantes entre
     * parenthèses (Builder::applyScopes → addNewWheresWithinGroup).
     *
     * SQL produit :
     *   where (profession_id not in (?) or profession_id is null)
     *     and deleted_at is null and ecole_setting_id = ?
     */
    #[Test]
    public function un_orWhere_ne_contourne_pas_lisolation_multi_ecoles(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $ecoleB = $this->ecole(['nom_ecole' => 'B']);

        $this->employee(['first_name' => 'LEGITIME', 'profession_id' => null], $ecoleA);
        $this->employee(['first_name' => 'AUTRE_ECOLE', 'profession_id' => null], $ecoleB);

        $this->actingAsRole('directeur', $ecoleA);

        $resultats = Employee::query()
            ->whereNotIn('profession_id', [999])
            ->orWhereNull('profession_id')
            ->get();

        $this->assertStringContainsString(
            '(`profession_id` not in (?) or `profession_id` is null)',
            Employee::query()->whereNotIn('profession_id', [999])->orWhereNull('profession_id')->toSql(),
            'Les conditions antérieures au scope doivent être parenthésées.',
        );

        $this->assertSame(0, $resultats->where('first_name', 'AUTRE_ECOLE')->count());
        $this->assertSame(1, $resultats->where('first_name', 'LEGITIME')->count());
    }

    #[Test]
    public function withoutGlobalScopes_permet_de_tout_voir(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $ecoleB = $this->ecole(['nom_ecole' => 'B']);

        $this->employee([], $ecoleA);
        $this->employee([], $ecoleB);

        $this->actingAsRole('directeur', $ecoleA);

        $this->assertCount(2, Employee::withoutGlobalScopes()->get());
    }
}
