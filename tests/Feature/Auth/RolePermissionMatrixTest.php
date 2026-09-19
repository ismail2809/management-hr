<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Verrouille la configuration de RolesPermissionsSeeder : c'est elle qui
 * décide réellement des autorisations (les gardes Resource::canDelete* ne
 * sont jamais appelées par Filament v4 — cf. BUGS.md BUG-01).
 */
class RolePermissionMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    #[Test]
    public function les_neuf_roles_attendus_existent(): void
    {
        $attendus = [
            'super-admin', 'directeur', 'secretaire', 'surveillante',
            'enseignant', 'assistante-transport',
            'femme-de-menage', 'chauffeur', 'gardien',
        ];

        $this->assertEqualsCanonicalizing($attendus, Role::pluck('name')->all());
    }

    #[Test]
    public function les_anciens_roles_du_saas_paie_sont_supprimes(): void
    {
        foreach (['admin', 'rh', 'manager', 'comptable', 'employee', 'enseignante'] as $obsolete) {
            $this->assertFalse(
                Role::where('name', $obsolete)->exists(),
                "Le rôle obsolète « $obsolete » ne doit plus exister.",
            );
        }
    }

    public static function matriceLecture(): array
    {
        return [
            // [rôle, permission, attendu]
            ['directeur',       'ViewAny:Employee',        true],
            ['secretaire',      'ViewAny:Employee',        true],
            ['surveillante',    'ViewAny:Employee',        true],
            ['enseignant',      'ViewAny:Employee',        false],
            ['chauffeur',       'ViewAny:Employee',        false],

            ['directeur',       'ApproveLeave',            true],
            ['secretaire',      'ApproveLeave',            true],
            ['surveillante',    'ApproveLeave',            true],
            ['enseignant',      'ApproveLeave',            false],

            ['enseignant',      'Create:Leave',            true],
            ['enseignant',      'Create:DocumentRequest',  true],
            ['enseignant',      'Update:Leave',            false],
            ['chauffeur',       'Create:Leave',            true],

            ['directeur',       'ViewAny:AuditLog',        true],
            ['secretaire',      'ViewAny:AuditLog',        false],
        ];
    }

    #[Test]
    #[DataProvider('matriceLecture')]
    public function la_matrice_des_permissions_est_respectee(string $role, string $permission, bool $attendu): void
    {
        $user = $this->userWithRole($role);

        $this->assertSame(
            $attendu,
            $user->can($permission),
            sprintf('%s devrait %savoir « %s ».', $role, $attendu ? '' : 'NE PAS ', $permission),
        );
    }

    #[Test]
    public function seuls_super_admin_et_directeur_peuvent_supprimer_un_employe(): void
    {
        $autorises = [];

        foreach (Role::pluck('name') as $role) {
            if ($this->userWithRole($role)->can('DeleteAny:Employee')) {
                $autorises[] = $role;
            }
        }

        // NOTE : « directeur » figure ici alors que le code applicatif entend
        // réserver la suppression au super-admin (BUG-01). Ce test constate la
        // réalité ; le corriger implique de retirer Delete*/DeleteAny* du
        // directeur dans RolesPermissionsSeeder.
        $this->assertEqualsCanonicalizing(['super-admin', 'directeur'], $autorises);
    }

    #[Test]
    public function le_super_admin_passe_toutes_les_autorisations_via_le_gate_de_shield(): void
    {
        $user = $this->userWithRole('super-admin');

        foreach (['DeleteAny:Employee', 'ViewAny:AuditLog', 'Update:Leave', 'PermissionInexistante'] as $ability) {
            $this->assertTrue($user->can($ability), "super-admin doit passer « $ability ».");
        }
    }

    #[Test]
    public function les_helpers_de_roles_du_modele_user_sont_coherents(): void
    {
        $this->assertTrue($this->userWithRole('chauffeur')->isBasicRole());
        $this->assertFalse($this->userWithRole('chauffeur')->isExtendedRole());

        $this->assertTrue($this->userWithRole('enseignant')->isBasicRole());
        $this->assertTrue($this->userWithRole('enseignant')->isExtendedRole());

        $this->assertFalse($this->userWithRole('directeur')->isBasicRole());
        $this->assertFalse($this->userWithRole('secretaire')->isBasicRole());
    }

    #[Test]
    public function canAccessPanel_accepte_le_personnel_et_refuse_un_compte_sans_role(): void
    {
        $panel = filament()->getPanel('app');

        foreach (['super-admin', 'directeur', 'secretaire', 'surveillante', 'enseignant', 'gardien'] as $role) {
            $this->assertTrue($this->userWithRole($role)->canAccessPanel($panel), $role);
        }

        $sansRole = User::create([
            'name'             => 'Sans rôle',
            'email'            => 'sansrole@test.ma',
            'password'         => bcrypt('password'),
            'ecole_setting_id' => $this->defaultEcole()->id,
        ]);

        $this->assertFalse($sansRole->canAccessPanel($panel));
    }
}
