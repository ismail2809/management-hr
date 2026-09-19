<?php

namespace Tests\Feature\Document;

use App\Models\DocumentRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Contrôleur de génération PDF : autorisations, liste blanche de types,
 * et effet de bord du téléchargement (passage en « approuvé »).
 */
class DocumentPdfControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    private function demande(array $attributes = [], ?int $ecoleId = null): DocumentRequest
    {
        $ecoleId ??= $this->defaultEcole()->id;

        return DocumentRequest::withoutGlobalScopes()->create(array_merge([
            'ecole_setting_id' => $ecoleId,
            'employee_id'      => $this->employee([], $this->defaultEcole())->id,
            'categorie'        => 'document',
            'type'             => 'attestation_travail',
            'format'           => 'digital',
            'status'           => 'en_attente',
        ], $attributes));
    }

    // NB : le cas « visiteur anonyme » est couvert dans tests/Feature/Bugs
    // (BUG-25) : il renvoie aujourd'hui un 500 au lieu d'une redirection.

    #[Test]
    public function lapercu_rend_un_pdf(): void
    {
        $this->actingAsRole('secretaire');
        $dr = $this->demande();

        $response = $this->get(route('documents.preview', $dr));

        $response->assertSuccessful();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
    }

    #[Test]
    public function lapercu_ne_modifie_pas_le_statut(): void
    {
        $this->actingAsRole('secretaire');
        $dr = $this->demande(['status' => 'en_attente']);

        $this->get(route('documents.preview', $dr))->assertSuccessful();

        $this->assertSame('en_attente', $dr->fresh()->status);
    }

    #[Test]
    public function le_telechargement_bascule_la_demande_en_approuve(): void
    {
        $user = $this->actingAsRole('secretaire');
        $dr   = $this->demande(['status' => 'en_attente']);

        $this->get(route('documents.pdf', $dr))->assertSuccessful();

        $dr->refresh();

        $this->assertSame('approuvé', $dr->status);
        $this->assertSame($user->id, $dr->processed_by);
        $this->assertNotNull($dr->processed_at);
    }

    #[Test]
    public function un_utilisateur_dune_autre_ecole_est_bloque(): void
    {
        $autreEcole = $this->ecole(['nom_ecole' => 'Autre']);
        $dr         = $this->demande([], $autreEcole->id);

        $this->actingAsRole('directeur', $this->defaultEcole());

        // 404 et non 403 : le CompanyScope s'applique dès la résolution du
        // route-model binding, donc l'enregistrement est introuvable avant même
        // que le abort_if(..., 403) du contrôleur ne soit évalué.
        // Cette garde du contrôleur est en pratique du code mort — l'isolation
        // est bien assurée, mais par un autre mécanisme que celui écrit.
        $this->get(route('documents.preview', $dr))->assertNotFound();
    }

    #[Test]
    public function le_super_admin_traverse_les_ecoles(): void
    {
        $autreEcole = $this->ecole(['nom_ecole' => 'Autre']);
        $dr         = $this->demande([], $autreEcole->id);

        $this->actingAsRole('super-admin');

        $this->get(route('documents.preview', $dr))->assertSuccessful();
    }

    #[Test]
    public function les_gabarits_fonctionnels_produisent_un_pdf(): void
    {
        // certificat_travail et ordre_mission sont volontairement absents :
        // leurs gabarits plantent (BUG-26, suite « Bugs »).
        $this->actingAsRole('secretaire');

        foreach (['attestation_travail', 'attestation_salaire', 'attestation_cnss'] as $type) {
            $dr = $this->demande(['type' => $type]);

            $this->get(route('documents.preview', $dr))
                ->assertSuccessful()
                ->assertHeader('content-type', 'application/pdf');
        }
    }

    #[Test]
    public function un_type_hors_liste_blanche_est_rejete(): void
    {
        $this->actingAsRole('secretaire');
        $dr = $this->demande(['type' => 'attestation_travail']);

        // Le contrôleur valide $type contre $documentTypes + $autreTypes.
        DocumentRequest::withoutGlobalScopes()->where('id', $dr->id)->update(['type' => null]);

        $this->get(route('documents.preview', $dr->fresh()))->assertStatus(400);
    }
}
