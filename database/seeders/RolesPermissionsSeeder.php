<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Créer toutes les permissions ───────────────────────────────────
        $allPerms = array_merge(
            $this->crudPerms('Employee'),
            $this->crudPerms('LeaveType'),
            $this->crudPerms('Leave'),
            $this->crudPerms('DocumentRequest'),
            $this->crudPerms('User'),
            $this->crudPerms('Role'),
            $this->viewOnlyPerms('AuditLog'),
            ['ApproveLeave', 'View:HrStatsOverview', 'View:MonEspace', 'View:Dashboard'],
        );
        foreach ($allPerms as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $employees   = $this->crudPerms('Employee');
        $leaveTypes  = $this->crudPerms('LeaveType');
        $leaves      = $this->crudPerms('Leave');
        $documents   = $this->crudPerms('DocumentRequest');
        $users       = $this->crudPerms('User');
        $roles       = $this->crudPerms('Role');
        $auditLogs   = $this->viewOnlyPerms('AuditLog');

        // Permissions sans suppression (secretaire / surveillante)
        $noCrudDelete = fn(array $perms) => array_filter($perms, fn($p) => ! str_starts_with($p, 'Delete'));

        $secretairePerms = array_values(array_filter(array_merge(
            $employees, $leaveTypes, $leaves,
            $documents, $users, $auditLogs,
            ['ApproveLeave', 'View:HrStatsOverview', 'View:MonEspace'],
        ), fn($p) => ! str_starts_with($p, 'Delete')));

        // ─── secretaire ────────────────────────────────────────────────────
        $secretaireRole = Role::firstOrCreate(['name' => 'secretaire', 'guard_name' => 'web']);
        $secretaireRole->syncPermissions(Permission::whereIn('name', $secretairePerms)->get());

        // ─── surveillante (mêmes droits que secretaire) ────────────────────
        $surveillanteRole = Role::firstOrCreate(['name' => 'surveillante', 'guard_name' => 'web']);
        $surveillanteRole->syncPermissions(Permission::whereIn('name', $secretairePerms)->get());

        // ─── directeur (secretaire + suppression soft) ─────────────────────
        $directeurPerms = array_merge(
            $secretairePerms,
            ["Delete:Employee", "DeleteAny:Employee",
             "Delete:Leave",    "DeleteAny:Leave",
             "Delete:DocumentRequest", "DeleteAny:DocumentRequest",
             "Delete:User",    "DeleteAny:User",
             "Delete:LeaveType", "DeleteAny:LeaveType"],
        );
        $directeurRole = Role::firstOrCreate(['name' => 'directeur', 'guard_name' => 'web']);
        $directeurRole->syncPermissions(Permission::whereIn('name', $directeurPerms)->get());

        // ─── employee ──────────────────────────────────────────────────────
        $employeePerms = [
            'View:Dashboard',
            'View:MonEspace',
            'ViewAny:Leave',           'View:Leave',           'Create:Leave',
            'ViewAny:DocumentRequest', 'View:DocumentRequest', 'Create:DocumentRequest',
            'View:Employee',           'Update:Employee',
        ];

        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employeeRole->syncPermissions(Permission::whereIn('name', $employeePerms)->get());

        // Supprimer les anciens rôles inutilisés
        Role::whereIn('name', ['admin', 'rh', 'manager', 'comptable'])->delete();

        $this->command->info('Rôles et permissions assignés avec succès.');
        $this->command->table(
            ['Rôle', '# Permissions', 'Accès'],
            [
                ['super-admin',  'Toutes',                                 'Plateforme complète + suppression'],
                ['directeur',    $directeurRole->permissions()->count(),    'Gestion complète + suppression soft'],
                ['secretaire',   $secretaireRole->permissions()->count(),   'Gestion complète sans suppression'],
                ['surveillante', $surveillanteRole->permissions()->count(),  'Gestion complète sans suppression'],
                ['employee',     $employeeRole->permissions()->count(),     'Espace perso, congés, demandes'],
            ]
        );
    }

    private function crudPerms(string $model): array
    {
        return [
            "ViewAny:{$model}", "View:{$model}",
            "Create:{$model}", "Update:{$model}",
            "Delete:{$model}", "DeleteAny:{$model}",
        ];
    }

    private function viewOnlyPerms(string $model): array
    {
        return ["ViewAny:{$model}", "View:{$model}"];
    }
}
