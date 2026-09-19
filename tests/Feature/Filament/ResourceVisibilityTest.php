<?php

namespace Tests\Feature\Filament;

use App\Filament\Admin\Resources\AnneeScolaireResource;
use App\Filament\Admin\Resources\AuditLogResource;
use App\Filament\Admin\Resources\AutreDemandeResource;
use App\Filament\Admin\Resources\DocumentAdministratifResource;
use App\Filament\Admin\Resources\DocumentTypeResource;
use App\Filament\Admin\Resources\EmployeeResource;
use App\Filament\Admin\Resources\GroupeResource;
use App\Filament\Admin\Resources\LeaveTypeResource;
use App\Filament\Admin\Resources\NiveauScolaireResource;
use App\Filament\Admin\Resources\ProfessionResource;
use App\Filament\Admin\Resources\Transports\TransportResource;
use App\Filament\Admin\Resources\UserResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Visibilité des resources Filament par rôle.
 * Verrouille l'état actuel : toute évolution des droits doit être délibérée.
 */
class ResourceVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    /** Les 10 resources du groupe « Paramétrage » + le journal d'audit. */
    public static function resourcesReserveesAuSuperAdmin(): array
    {
        return [
            'AnneeScolaire'  => [AnneeScolaireResource::class],
            'DocumentType'   => [DocumentTypeResource::class],
            'LeaveType'      => [LeaveTypeResource::class],
            'Profession'     => [ProfessionResource::class],
            'NiveauScolaire' => [NiveauScolaireResource::class],
            'Groupe'         => [GroupeResource::class],
            'Transport'      => [TransportResource::class],
            'AuditLog'       => [AuditLogResource::class],
        ];
    }

    #[Test]
    #[DataProvider('resourcesReserveesAuSuperAdmin')]
    public function le_parametrage_est_reserve_au_super_admin(string $resource): void
    {
        $this->actingAsRole('super-admin');
        $this->assertTrue($resource::canViewAny(), 'super-admin doit voir ' . class_basename($resource));

        auth()->logout();

        // NOTE : le directeur est exclu alors que RolesPermissionsSeeder lui
        // accorde le CRUD sur LeaveType — contradiction documentée (BUG-12).
        $this->actingAsRole('directeur');
        $this->assertFalse($resource::canViewAny(), 'directeur ne voit pas ' . class_basename($resource));
    }

    #[Test]
    public function les_employes_sont_visibles_par_le_personnel_encadrant(): void
    {
        foreach (['super-admin', 'directeur', 'secretaire'] as $role) {
            $this->actingAsRole($role);
            $this->assertTrue(EmployeeResource::canViewAny(), $role);
            auth()->logout();
        }
    }

    #[Test]
    public function les_employes_sont_masques_a_la_surveillante_et_aux_roles_limites(): void
    {
        foreach (['surveillante', 'enseignant', 'gardien'] as $role) {
            $this->actingAsRole($role);
            $this->assertFalse(EmployeeResource::canViewAny(), $role);
            auth()->logout();
        }
    }

    #[Test]
    public function un_role_limite_ne_peut_consulter_que_sa_propre_fiche(): void
    {
        $moi    = $this->employee();
        $autrui = $this->employee();

        $this->actingAsRole('enseignant', null, $moi);

        $this->assertTrue(EmployeeResource::canView($moi));
        $this->assertFalse(EmployeeResource::canView($autrui));
        $this->assertTrue(EmployeeResource::canEdit($moi));
        $this->assertFalse(EmployeeResource::canEdit($autrui));
        $this->assertFalse(EmployeeResource::canCreate());
    }

    #[Test]
    public function la_gestion_des_utilisateurs_exclut_la_surveillante_et_les_roles_limites(): void
    {
        foreach (['super-admin', 'directeur', 'secretaire'] as $role) {
            $this->actingAsRole($role);
            $this->assertTrue(UserResource::canViewAny(), $role);
            auth()->logout();
        }

        foreach (['surveillante', 'enseignant', 'chauffeur'] as $role) {
            $this->actingAsRole($role);
            $this->assertFalse(UserResource::canViewAny(), $role);
            auth()->logout();
        }
    }

    #[Test]
    public function un_role_limite_ne_modifie_sa_demande_que_tant_quelle_est_en_attente(): void
    {
        $moi = $this->employee();
        $this->actingAsRole('secretaire');

        $enAttente = \App\Models\DocumentRequest::create([
            'employee_id' => $moi->id, 'categorie' => 'document',
            'type' => 'attestation_travail', 'format' => 'digital', 'status' => 'en_attente',
        ]);
        $approuvee = \App\Models\DocumentRequest::create([
            'employee_id' => $moi->id, 'categorie' => 'document',
            'type' => 'attestation_travail', 'format' => 'digital', 'status' => 'approuvé',
        ]);

        auth()->logout();
        $this->actingAsRole('enseignant', null, $moi);

        $this->assertTrue(DocumentAdministratifResource::canEdit($enAttente));
        $this->assertFalse(DocumentAdministratifResource::canEdit($approuvee));
    }

    #[Test]
    public function les_libelles_de_navigation_sadaptent_aux_roles_limites(): void
    {
        $employe = $this->employee();

        $this->actingAsRole('directeur');
        $this->assertSame('Documents administratifs', DocumentAdministratifResource::getNavigationLabel());
        auth()->logout();

        $this->actingAsRole('enseignant', null, $employe);
        $this->assertSame('Mes documents', DocumentAdministratifResource::getNavigationLabel());
        $this->assertSame('Mes autres demandes', AutreDemandeResource::getNavigationLabel());
    }
}
