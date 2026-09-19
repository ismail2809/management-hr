<?php

namespace Tests;

use App\Models\EcoleSettings;
use App\Models\Employee;
use App\Models\Profession;
use App\Models\User;
use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    /** École par défaut du test courant. */
    protected ?EcoleSettings $ecole = null;

    /**
     * Joue le vrai RolesPermissionsSeeder pour que les tests valident la
     * configuration de rôles réellement livrée en production, et non une
     * copie de circonstance.
     */
    protected function seedRoles(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // RolesPermissionsSeeder ne crée pas super-admin : en production il vient
        // de SuperAdminSeeder / EcoleSettingsSeeder. Son autorisation passe ensuite
        // par le Gate::before de Shield (config filament-shield.super_admin).
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        $this->seed(RolesPermissionsSeeder::class);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function ecole(array $attributes = []): EcoleSettings
    {
        return EcoleSettings::create(array_merge([
            'nom_ecole' => 'École de test',
            'pays'      => 'Maroc',
        ], $attributes));
    }

    /** École par défaut, créée à la demande et mémorisée. */
    protected function defaultEcole(): EcoleSettings
    {
        return $this->ecole ??= $this->ecole();
    }

    protected function profession(string $name, ?EcoleSettings $ecole = null): Profession
    {
        return Profession::withoutGlobalScopes()->create([
            'ecole_setting_id' => ($ecole ?? $this->defaultEcole())->id,
            'name'             => $name,
        ]);
    }

    protected function employee(array $attributes = [], ?EcoleSettings $ecole = null): Employee
    {
        static $n = 0;
        $n++;

        return Employee::withoutGlobalScopes()->create(array_merge([
            'ecole_setting_id' => ($ecole ?? $this->defaultEcole())->id,
            'first_name'       => 'Prenom' . $n,
            'last_name'        => 'NOM' . $n,
            'status'           => 'actif',
        ], $attributes));
    }

    protected function userWithRole(
        string $role,
        ?EcoleSettings $ecole = null,
        ?Employee $employee = null,
    ): User {
        static $n = 0;
        $n++;

        $user = User::create([
            'name'             => ucfirst($role) . ' ' . $n,
            'email'            => $role . $n . '@test.ma',
            'password'         => bcrypt('password'),
            'ecole_setting_id' => $role === 'super-admin' ? null : ($ecole ?? $this->defaultEcole())->id,
            'employee_id'      => $employee?->id,
        ]);

        $user->syncRoles([$role]);

        return $user->fresh();
    }

    /** Crée un utilisateur du rôle donné et l'authentifie. */
    protected function actingAsRole(
        string $role,
        ?EcoleSettings $ecole = null,
        ?Employee $employee = null,
    ): User {
        $user = $this->userWithRole($role, $ecole, $employee);
        $this->actingAs($user);

        return $user;
    }
}
