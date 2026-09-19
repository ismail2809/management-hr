<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Accès HTTP aux deux panels.
 * Rappel du piège de nommage : panel « app » => /admin, panel « admin » => /super-admin.
 */
class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    #[Test]
    public function un_visiteur_anonyme_est_redirige_vers_la_connexion(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public static function rolesDuPersonnel(): array
    {
        return [['directeur'], ['secretaire'], ['surveillante']];
    }

    /**
     * Un rôle par test : le middleware AuthenticateSession de Filament
     * invalide la session dès qu'un second utilisateur est authentifié dans
     * la même requête HTTP simulée. Boucler sur les rôles dans un seul test
     * produirait des 302 erratiques vers /admin/login.
     */
    #[Test]
    #[DataProvider('rolesDuPersonnel')]
    public function le_personnel_accede_au_panel_app(string $role): void
    {
        $this->actingAsRole($role);

        $this->get('/admin')->assertSuccessful();
    }

    #[Test]
    public function un_role_a_acces_limite_accede_aussi_au_panel_app(): void
    {
        $employe = $this->employee();
        $this->actingAsRole('enseignant', null, $employe);

        $this->get('/admin')->assertSuccessful();
    }

    #[Test]
    public function un_compte_sans_ecole_est_refuse_par_RequireAppRole(): void
    {
        $user = $this->userWithRole('secretaire');
        $user->update(['ecole_setting_id' => null]);

        $this->actingAs($user->fresh())
            ->get('/admin')
            ->assertForbidden();
    }

    #[Test]
    public function un_compte_sans_role_est_refuse(): void
    {
        $user = User::create([
            'name'             => 'Intrus',
            'email'            => 'intrus@test.ma',
            'password'         => bcrypt('password'),
            'ecole_setting_id' => $this->defaultEcole()->id,
        ]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    #[Test]
    public function seul_le_super_admin_accede_au_panel_super_admin(): void
    {
        $this->actingAsRole('super-admin');
        $this->get('/super-admin')->assertSuccessful();
    }

    #[Test]
    public function le_directeur_est_refuse_sur_le_panel_super_admin(): void
    {
        $this->actingAsRole('directeur');

        $this->get('/super-admin')->assertForbidden();
    }

    #[Test]
    public function le_super_admin_accede_egalement_au_panel_app_sans_ecole(): void
    {
        // RequireAppRole exempte explicitement le super-admin de l'exigence
        // d'un ecole_setting_id.
        $this->actingAsRole('super-admin');

        $this->get('/admin')->assertSuccessful();
    }

    #[Test]
    public function les_pages_legales_restent_publiques(): void
    {
        $this->get('/politique-confidentialite')->assertSuccessful();
        $this->get('/mentions-legales')->assertSuccessful();
    }
}
