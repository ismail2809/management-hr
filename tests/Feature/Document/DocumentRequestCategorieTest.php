<?php

namespace Tests\Feature\Document;

use App\Filament\Admin\Resources\AutreDemandeResource;
use App\Filament\Admin\Resources\DocumentAdministratifResource;
use App\Models\DocumentRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * La table document_requests alimente DEUX resources Filament, séparées par
 * la colonne `categorie`. Ces tests verrouillent cette scission ainsi que la
 * règle anti-conflit des demandes de photocopie.
 */
class DocumentRequestCategorieTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    private function demande(array $attributes = []): DocumentRequest
    {
        return DocumentRequest::create(array_merge([
            'employee_id' => $this->employee()->id,
            'categorie'   => 'document',
            'type'        => 'attestation_travail',
            'format'      => 'digital',
            'status'      => 'en_attente',
        ], $attributes));
    }

    #[Test]
    public function la_resource_documents_ne_voit_que_la_categorie_document(): void
    {
        $this->actingAsRole('directeur');

        $this->demande(['categorie' => 'document', 'type' => 'attestation_travail']);
        $this->demande(['categorie' => 'autre', 'type' => 'photocopie']);

        $ids = DocumentAdministratifResource::getEloquentQuery()->pluck('categorie')->unique();

        $this->assertSame(['document'], $ids->values()->all());
    }

    #[Test]
    public function la_resource_autres_demandes_ne_voit_que_la_categorie_autre(): void
    {
        $this->actingAsRole('directeur');

        $this->demande(['categorie' => 'document', 'type' => 'attestation_travail']);
        $this->demande(['categorie' => 'autre', 'type' => 'photocopie']);

        $ids = AutreDemandeResource::getEloquentQuery()->pluck('categorie')->unique();

        $this->assertSame(['autre'], $ids->values()->all());
    }

    #[Test]
    public function un_role_a_acces_limite_ne_voit_que_ses_propres_demandes(): void
    {
        $moi    = $this->employee(['first_name' => 'Moi']);
        $autrui = $this->employee(['first_name' => 'Autrui']);

        $this->actingAsRole('secretaire');
        $this->demande(['categorie' => 'autre', 'type' => 'photocopie', 'employee_id' => $moi->id]);
        $this->demande(['categorie' => 'autre', 'type' => 'photocopie', 'employee_id' => $autrui->id]);
        auth()->logout();

        $this->actingAsRole('enseignant', null, $moi);

        $resultats = AutreDemandeResource::getEloquentQuery()->get();

        $this->assertCount(1, $resultats);
        $this->assertSame($moi->id, $resultats->first()->employee_id);
    }

    #[Test]
    public function les_roles_basiques_nont_pas_acces_aux_autres_demandes(): void
    {
        $employe = $this->employee();

        $this->actingAsRole('gardien', null, $employe);
        $this->assertFalse(AutreDemandeResource::canViewAny(), 'gardien = rôle basic');

        auth()->logout();

        $this->actingAsRole('enseignant', null, $employe);
        $this->assertTrue(AutreDemandeResource::canViewAny(), 'enseignant = rôle extended');
    }

    #[Test]
    public function la_secretaire_est_en_lecture_seule_sur_les_autres_demandes(): void
    {
        $this->actingAsRole('secretaire');

        $this->assertFalse(AutreDemandeResource::canCreate());
        $this->assertTrue(DocumentAdministratifResource::canCreate());
    }

    #[Test]
    public function la_surveillante_est_en_lecture_seule_sur_les_documents_administratifs(): void
    {
        $this->actingAsRole('surveillante');

        $this->assertFalse(DocumentAdministratifResource::canCreate());
        $this->assertTrue(AutreDemandeResource::canCreate());
    }

    #[Test]
    public function deux_evaluations_le_meme_jour_pour_le_meme_groupe_sont_refusees(): void
    {
        $this->actingAsRole('surveillante');

        $this->demande([
            'categorie'                 => 'autre',
            'type'                      => 'photocopie',
            'photocopie_sous_type'      => 'Examen',
            'photocopie_niveau'         => 'Primaire',
            'photocopie_groupe'         => '3A',
            'photocopie_date_souhaitee' => '2026-10-15',
        ]);

        $this->expectException(ValidationException::class);

        AutreDemandeResource::checkPhotocopieConflict([
            'type'                      => 'photocopie',
            'photocopie_sous_type'      => 'Contrôle continu',
            'photocopie_niveau'         => 'Primaire',
            'photocopie_groupe'         => '3A',
            'photocopie_date_souhaitee' => '2026-10-15',
        ]);
    }

    #[Test]
    public function un_autre_groupe_le_meme_jour_reste_autorise(): void
    {
        $this->actingAsRole('surveillante');

        $this->demande([
            'categorie'                 => 'autre',
            'type'                      => 'photocopie',
            'photocopie_sous_type'      => 'Examen',
            'photocopie_niveau'         => 'Primaire',
            'photocopie_groupe'         => '3A',
            'photocopie_date_souhaitee' => '2026-10-15',
        ]);

        AutreDemandeResource::checkPhotocopieConflict([
            'type'                      => 'photocopie',
            'photocopie_sous_type'      => 'Examen',
            'photocopie_niveau'         => 'Primaire',
            'photocopie_groupe'         => '3B',
            'photocopie_date_souhaitee' => '2026-10-15',
        ]);

        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function un_niveau_hors_perimetre_echappe_a_la_regle_de_conflit(): void
    {
        $this->actingAsRole('surveillante');

        $this->demande([
            'categorie'                 => 'autre',
            'type'                      => 'photocopie',
            'photocopie_sous_type'      => 'Examen',
            'photocopie_niveau'         => 'Collège',
            'photocopie_groupe'         => '3A',
            'photocopie_date_souhaitee' => '2026-10-15',
        ]);

        // hr.photocopie_conflict_niveaux ne contient que « Primaire ».
        AutreDemandeResource::checkPhotocopieConflict([
            'type'                      => 'photocopie',
            'photocopie_sous_type'      => 'Examen',
            'photocopie_niveau'         => 'Collège',
            'photocopie_groupe'         => '3A',
            'photocopie_date_souhaitee' => '2026-10-15',
        ]);

        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function une_demande_refusee_ne_bloque_pas_le_creneau(): void
    {
        $this->actingAsRole('surveillante');

        $this->demande([
            'categorie'                 => 'autre',
            'type'                      => 'photocopie',
            'status'                    => 'refusé',
            'photocopie_sous_type'      => 'Examen',
            'photocopie_niveau'         => 'Primaire',
            'photocopie_groupe'         => '3A',
            'photocopie_date_souhaitee' => '2026-10-15',
        ]);

        AutreDemandeResource::checkPhotocopieConflict([
            'type'                      => 'photocopie',
            'photocopie_sous_type'      => 'Examen',
            'photocopie_niveau'         => 'Primaire',
            'photocopie_groupe'         => '3A',
            'photocopie_date_souhaitee' => '2026-10-15',
        ]);

        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function la_demande_en_cours_dedition_ne_se_bloque_pas_elle_meme(): void
    {
        $this->actingAsRole('surveillante');

        $existante = $this->demande([
            'categorie'                 => 'autre',
            'type'                      => 'photocopie',
            'photocopie_sous_type'      => 'Examen',
            'photocopie_niveau'         => 'Primaire',
            'photocopie_groupe'         => '3A',
            'photocopie_date_souhaitee' => '2026-10-15',
        ]);

        AutreDemandeResource::checkPhotocopieConflict([
            'type'                      => 'photocopie',
            'photocopie_sous_type'      => 'Examen',
            'photocopie_niveau'         => 'Primaire',
            'photocopie_groupe'         => '3A',
            'photocopie_date_souhaitee' => '2026-10-15',
        ], $existante->id);

        $this->expectNotToPerformAssertions();
    }
}
