<?php

namespace Tests\Feature\MultiTenancy;

use App\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * GlobalOrCompanyScope — tables de référence à ecole_setting_id NULLABLE :
 * on voit les lignes globales (NULL) PLUS celles de son école.
 */
class GlobalOrCompanyScopeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    private function documentType(string $code, ?int $ecoleId): DocumentType
    {
        return DocumentType::withoutGlobalScopes()->create([
            'ecole_setting_id' => $ecoleId,
            'code'             => $code,
            'name'             => ucfirst($code),
            'categorie'        => 'document',
            'active'           => true,
            'sort_order'       => 1,
        ]);
    }

    #[Test]
    public function les_lignes_globales_sont_visibles_par_toutes_les_ecoles(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $this->documentType('global_type', null);

        $this->actingAsRole('directeur', $ecoleA);

        $this->assertCount(1, DocumentType::all());
        $this->assertNull(DocumentType::first()->ecole_setting_id);
    }

    #[Test]
    public function une_ligne_propre_a_une_ecole_nest_visible_que_par_elle(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $ecoleB = $this->ecole(['nom_ecole' => 'B']);

        $this->documentType('global_type', null);
        $this->documentType('type_ecole_a', $ecoleA->id);

        $this->actingAsRole('directeur', $ecoleA);
        $this->assertCount(2, DocumentType::all(), 'École A : la globale + la sienne.');

        auth()->logout();

        $this->actingAsRole('directeur', $ecoleB);
        $this->assertCount(1, DocumentType::all(), 'École B : uniquement la globale.');
    }

    #[Test]
    public function withoutGlobalScopes_revele_toutes_les_lignes(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $ecoleB = $this->ecole(['nom_ecole' => 'B']);

        $this->documentType('global_type', null);
        $this->documentType('type_a', $ecoleA->id);
        $this->documentType('type_b', $ecoleB->id);

        $this->actingAsRole('directeur', $ecoleA);

        $this->assertCount(3, DocumentType::withoutGlobalScopes()->get());
    }

    #[Test]
    public function sans_utilisateur_authentifie_le_scope_ne_filtre_pas(): void
    {
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $this->documentType('global_type', null);
        $this->documentType('type_a', $ecoleA->id);

        // Contexte console / file d'attente : auth()->check() est faux.
        $this->assertCount(2, DocumentType::all());
    }

    #[Test]
    public function le_super_admin_ne_beneficie_PAS_du_contournement_ici(): void
    {
        // Asymétrie assumée avec CompanyScope, qui lui court-circuite le filtre
        // pour super-admin. Documenté dans CLAUDE.md ; ce test verrouille le
        // comportement actuel pour qu'un changement soit délibéré.
        $ecoleA = $this->ecole(['nom_ecole' => 'A']);
        $this->documentType('global_type', null);
        $this->documentType('type_a', $ecoleA->id);

        $this->actingAsRole('super-admin');

        // super-admin a ecole_setting_id = NULL : il ne voit que les globales.
        $this->assertCount(1, DocumentType::all());
    }
}
