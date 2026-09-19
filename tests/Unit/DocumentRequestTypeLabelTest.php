<?php

namespace Tests\Unit;

use App\Models\DocumentRequest;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Libellés des types de demande (accesseur type_label utilisé par les widgets
 * du tableau de bord) et cohérence des deux catalogues statiques.
 */
class DocumentRequestTypeLabelTest extends TestCase
{
    #[Test]
    public function le_libelle_est_resolu_pour_un_type_de_document(): void
    {
        $dr = new DocumentRequest(['type' => 'attestation_travail']);

        $this->assertSame('Attestation de travail', $dr->type_label);
    }

    #[Test]
    public function le_libelle_est_resolu_pour_une_autre_demande(): void
    {
        $dr = new DocumentRequest(['type' => 'photocopie']);

        $this->assertSame('Photocopie', $dr->type_label);
    }

    #[Test]
    public function un_type_inconnu_retombe_sur_sa_valeur_brute(): void
    {
        $dr = new DocumentRequest(['type' => 'type_maison']);

        $this->assertSame('type_maison', $dr->type_label);
    }

    #[Test]
    public function les_deux_catalogues_nont_aucun_code_en_commun(): void
    {
        $collision = array_intersect(
            array_keys(DocumentRequest::$documentTypes),
            array_keys(DocumentRequest::$autreTypes),
        );

        $this->assertSame(
            [],
            $collision,
            'Un même code dans les deux catalogues rendrait la scission par catégorie ambiguë.',
        );
    }

    #[Test]
    public function tous_les_codes_des_catalogues_existent_dans_lenum_sql(): void
    {
        $codes = array_merge(
            array_keys(DocumentRequest::$documentTypes),
            array_keys(DocumentRequest::$autreTypes),
        );

        // Garde-fou : un code ajouté en PHP sans migration provoquerait une
        // QueryException « Data truncated for column 'type' » à l'insertion.
        $this->assertCount(16, $codes);
    }
}
