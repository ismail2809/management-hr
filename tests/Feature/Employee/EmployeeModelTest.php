<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\Groupe;
use App\Models\NiveauScolaire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmployeeModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->actingAsRole('directeur');
    }

    #[Test]
    public function un_uuid_est_genere_a_la_creation(): void
    {
        $employe = $this->employee();

        $this->assertTrue(Str::isUuid($employe->uuid));
    }

    #[Test]
    public function les_urls_utilisent_luuid_et_non_lid(): void
    {
        $employe = $this->employee();

        $this->assertSame('uuid', $employe->getRouteKeyName());
        $this->assertSame($employe->uuid, $employe->getRouteKey());
    }

    #[Test]
    public function le_nom_complet_concatene_prenom_et_nom(): void
    {
        $employe = $this->employee(['first_name' => 'Yassine', 'last_name' => 'EL AMRANI']);

        $this->assertSame('Yassine EL AMRANI', $employe->full_name);
    }

    #[Test]
    public function isProfesseur_reconnait_les_trois_orthographes(): void
    {
        foreach (['Professeur', 'Enseignant', 'Enseignante'] as $nom) {
            $employe = $this->employee(['profession_id' => $this->profession($nom)->id]);

            $this->assertTrue($employe->fresh()->isProfesseur(), $nom);
        }
    }

    #[Test]
    public function isProfesseur_est_faux_pour_les_autres_metiers(): void
    {
        foreach (['Chauffeur', 'Gardien', 'Secrétaire'] as $nom) {
            $employe = $this->employee(['profession_id' => $this->profession($nom)->id]);

            $this->assertFalse($employe->fresh()->isProfesseur(), $nom);
        }
    }

    #[Test]
    public function isProfesseur_est_faux_sans_profession(): void
    {
        $this->assertFalse($this->employee(['profession_id' => null])->isProfesseur());
    }

    #[Test]
    public function la_suppression_est_douce(): void
    {
        $employe = $this->employee();
        $id      = $employe->id;

        $employe->delete();

        $this->assertNull(Employee::find($id));
        $this->assertNotNull(Employee::withTrashed()->find($id));
    }

    #[Test]
    public function un_employe_supprime_disparait_des_relations_belongsTo(): void
    {
        $employe = $this->employee();

        $doc = EmployeeDocument::create([
            'employee_id'   => $employe->id,
            'type_document' => 'autre',
            'name'          => 'Contrat',
            'file_path'     => 'documents/contrat.pdf',
        ]);

        $employe->delete();

        $this->assertNull(
            $doc->fresh()->employee,
            'Une relation belongsTo vers un modèle soft-deleted renvoie null.',
        );
    }

    #[Test]
    public function un_employe_peut_etre_rattache_a_des_groupes(): void
    {
        $niveau = NiveauScolaire::create(['name' => 'Primaire', 'order' => 1]);
        $g1     = Groupe::create(['niveau_scolaire_id' => $niveau->id, 'name' => '1A']);
        $g2     = Groupe::create(['niveau_scolaire_id' => $niveau->id, 'name' => '1B']);

        $employe = $this->employee();
        $employe->groupes()->attach([$g1->id, $g2->id]);

        $this->assertCount(2, $employe->fresh()->groupes);
        $this->assertCount(1, $g1->fresh()->employees);
    }

    #[Test]
    public function les_activites_daudit_sont_enregistrees(): void
    {
        $employe = $this->employee(['first_name' => 'Avant']);
        $employe->update(['first_name' => 'Apres']);

        $this->assertDatabaseHas('activity_log', [
            'log_name'      => 'employee',
            'subject_id'    => $employe->id,
            'subject_type'  => Employee::class,
            'event'         => 'updated',
        ]);
    }
}
