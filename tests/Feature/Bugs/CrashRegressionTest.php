<?php

namespace Tests\Feature\Bugs;

use App\Models\DocumentRequest;
use App\Models\Leave;
use App\Services\EmployeeImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ⚠️ Ces tests ÉCHOUENT tant que les anomalies ne sont pas corrigées.
 * Ils décrivent le comportement ATTENDU, pas le comportement actuel.
 * Suite exclue du lot par défaut : php artisan test --testsuite=Bugs
 *
 * Toutes les anomalies ci-dessous produisent une page d'erreur 500.
 */
#[Group('bugs')]
class CrashRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    private function demande(array $attributes = []): DocumentRequest
    {
        return DocumentRequest::withoutGlobalScopes()->create(array_merge([
            'ecole_setting_id' => $this->defaultEcole()->id,
            'employee_id'      => $this->employee()->id,
            'categorie'        => 'document',
            'type'             => 'attestation_travail',
            'format'           => 'digital',
            'status'           => 'en_attente',
        ], $attributes));
    }

    // ───────────────────────── BUG-04 ─────────────────────────

    public static function typesSansGabarit(): array
    {
        return [['bulletin_paie'], ['attestation_ir'], ['credit_irrevocable']];
    }

    /**
     * BUG-04 — DocumentPdfController retombe sur `pdf.documents.generic`,
     * qui n'existe pas → InvalidArgumentException.
     * Correctif : créer resources/views/pdf/documents/generic.blade.php.
     */
    #[Test]
    #[DataProvider('typesSansGabarit')]
    public function bug04_un_type_sans_gabarit_ne_doit_pas_planter(string $type): void
    {
        $this->actingAsRole('secretaire');

        $this->get(route('documents.preview', $this->demande(['type' => $type])))
            ->assertSuccessful();
    }

    #[Test]
    public function bug04_le_gabarit_de_repli_doit_exister(): void
    {
        $this->assertTrue(
            view()->exists('pdf.documents.generic'),
            'DocumentPdfController:48 y renvoie tous les types sans gabarit dédié.',
        );
    }

    // ───────────────────────── BUG-26 ─────────────────────────

    public static function gabaritsCasses(): array
    {
        return [['certificat_travail'], ['ordre_mission']];
    }

    /**
     * BUG-26 — `{{ — }}` : un tiret cadratin nu dans une expression Blade,
     * interprété comme une constante PHP inexistante.
     * Ligne « Département », vestige de la table `departments` supprimée.
     * Correctif : remplacer `{{ — }}` par `—` (hors accolades).
     */
    #[Test]
    #[DataProvider('gabaritsCasses')]
    public function bug26_les_gabarits_livres_ne_doivent_pas_planter(string $type): void
    {
        $this->actingAsRole('secretaire');

        $this->get(route('documents.preview', $this->demande(['type' => $type])))
            ->assertSuccessful()
            ->assertHeader('content-type', 'application/pdf');
    }

    #[Test]
    #[DataProvider('gabaritsCasses')]
    public function bug26_aucun_tiret_nu_ne_subsiste_dans_les_gabarits(string $type): void
    {
        $source = file_get_contents(resource_path("views/pdf/documents/$type.blade.php"));

        $this->assertDoesNotMatchRegularExpression(
            '/\{\{\s*—\s*\}\}/u',
            $source,
            "Le gabarit $type contient un tiret cadratin nu dans une expression Blade.",
        );
    }

    // ───────────────────────── BUG-25 ─────────────────────────

    /**
     * BUG-25 — Les routes /documents/{id}/pdf|preview sont protégées par le
     * middleware `auth`, dont le handler appelle route('login'). Or aucune
     * route ne porte ce nom (seulement filament.app.auth.login) →
     * RouteNotFoundException → 500 au lieu d'une redirection.
     * Correctif : Authenticate::redirectUsing(fn () => route('filament.app.auth.login'))
     * dans AppServiceProvider::boot().
     */
    #[Test]
    public function bug25_un_visiteur_anonyme_doit_etre_redirige_et_non_planter(): void
    {
        $dr = $this->demande();

        $this->get(route('documents.preview', $dr))
            ->assertRedirect('/admin/login');
    }

    #[Test]
    public function bug25_une_route_de_connexion_nommee_doit_exister(): void
    {
        $this->assertTrue(
            app('router')->has('login'),
            "Le middleware auth par défaut de Laravel résout route('login').",
        );
    }

    // BUG-11 : requalifié comme NON reproductible (le schéma interdit les
    // dates nulles). La preuve vit dans LeaveWorkflowTest, suite principale.

    // ───────────────────────── BUG-23 ─────────────────────────

    /**
     * BUG-23 — IOFactory::load() n'est entouré d'aucun try/catch, ni dans le
     * service ni dans l'action Filament, alors que le champ accepte
     * application/octet-stream. Correctif : capturer l'exception et afficher
     * une notification.
     */
    #[Test]
    public function bug23_un_fichier_illisible_doit_etre_signale_et_non_planter(): void
    {
        $chemin = tempnam(sys_get_temp_dir(), 'faux_') . '.xlsx';
        file_put_contents($chemin, "%PDF-1.4\n%\xE2\xE3\xCF\xD3\ntrailer\n");

        try {
            $resultat = (new EmployeeImportService())->import($chemin, $this->defaultEcole()->id);

            $this->assertSame(0, $resultat['imported']);
            $this->assertNotEmpty($resultat['errors'], 'Le service doit renvoyer une erreur exploitable.');
        } finally {
            @unlink($chemin);
        }
    }
}
