<?php

namespace Tests\Unit;

use App\Rules\SafeFileUpload;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Règle SafeFileUpload : liste noire d'extensions exécutables sur tous les
 * champs d'upload (justificatifs de congé, pièces jointes, fichier final).
 */
class SafeFileUploadRuleTest extends TestCase
{
    private function echecs(mixed $value): array
    {
        $erreurs = [];
        (new SafeFileUpload())->validate('fichier', $value, function ($msg) use (&$erreurs) {
            $erreurs[] = $msg;
        });

        return $erreurs;
    }

    public static function extensionsInterdites(): array
    {
        return array_map(
            fn ($ext) => [$ext],
            ['php', 'php5', 'phtml', 'phar', 'sh', 'exe', 'bat', 'js', 'svg', 'ps1', 'py'],
        );
    }

    public static function extensionsAutorisees(): array
    {
        return array_map(fn ($ext) => [$ext], ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'docx', 'xlsx']);
    }

    #[Test]
    #[DataProvider('extensionsInterdites')]
    public function les_extensions_executables_sont_rejetees(string $ext): void
    {
        $erreurs = $this->echecs(UploadedFile::fake()->create("charge-utile.$ext", 10));

        $this->assertNotEmpty($erreurs, "L'extension .$ext doit être refusée.");
        $this->assertStringContainsString($ext, $erreurs[0]);
    }

    #[Test]
    #[DataProvider('extensionsAutorisees')]
    public function les_extensions_bureautiques_sont_acceptees(string $ext): void
    {
        $this->assertSame([], $this->echecs(UploadedFile::fake()->create("document.$ext", 10)));
    }

    #[Test]
    public function la_casse_de_lextension_est_ignoree(): void
    {
        $this->assertNotEmpty($this->echecs(UploadedFile::fake()->create('SHELL.PHP', 10)));
    }

    #[Test]
    public function la_regle_accepte_un_tableau_de_fichiers(): void
    {
        $erreurs = $this->echecs([
            UploadedFile::fake()->create('ok.pdf', 10),
            UploadedFile::fake()->create('mechant.php', 10),
        ]);

        $this->assertNotEmpty($erreurs);
    }

    #[Test]
    public function une_valeur_qui_nest_pas_un_fichier_est_ignoree(): void
    {
        $this->assertSame([], $this->echecs(null));
        $this->assertSame([], $this->echecs('chemin/deja/stocke.pdf'));
    }
}
