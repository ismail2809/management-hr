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

        // ─── secretaire : Dashboard + MonEspace + Congés (gérer) + Docs Admin (gérer) + Autres Demandes (voir) ──
        $secretairePerms = [
            'View:Dashboard', 'View:MonEspace',
            'ViewAny:Leave', 'View:Leave', 'Create:Leave', 'Update:Leave', 'ApproveLeave',
            'ViewAny:DocumentRequest', 'View:DocumentRequest', 'Create:DocumentRequest', 'Update:DocumentRequest',
            'ViewAny:Employee', 'View:Employee', 'Create:Employee', 'Update:Employee',
            'ViewAny:User', 'View:User', 'Create:User', 'Update:User',
        ];
        $secretaireRole = Role::firstOrCreate(['name' => 'secretaire', 'guard_name' => 'web']);
        $secretaireRole->syncPermissions(Permission::whereIn('name', $secretairePerms)->get());

        // ─── surveillante : Dashboard + MonEspace + Congés (voir) + Docs Admin (voir) + Autres Demandes (gérer) ──
        $surveillantePerms = [
            'View:Dashboard', 'View:MonEspace',
            'ViewAny:Leave', 'View:Leave', 'Create:Leave', 'Update:Leave', 'ApproveLeave',
            'ViewAny:DocumentRequest', 'View:DocumentRequest', 'Create:DocumentRequest', 'Update:DocumentRequest',
            'ViewAny:Employee', 'View:Employee', 'Create:Employee', 'Update:Employee',
            'ViewAny:User', 'View:User', 'Create:User', 'Update:User',
        ];
        $surveillanteRole = Role::firstOrCreate(['name' => 'surveillante', 'guard_name' => 'web']);
        $surveillanteRole->syncPermissions(Permission::whereIn('name', $surveillantePerms)->get());

        // ─── directeur : accès complet + suppression ───────────────────────
        $directeurPerms = array_values(array_filter(array_merge(
            $employees, $leaveTypes, $leaves,
            $documents, $users, $auditLogs,
            ['ApproveLeave', 'View:HrStatsOverview', 'View:MonEspace', 'View:Dashboard'],
        ), fn($p) => ! str_starts_with($p, 'Delete')));
        $directeurPerms = array_merge($directeurPerms, [
            "Delete:Employee", "DeleteAny:Employee",
            "Delete:Leave",    "DeleteAny:Leave",
            "Delete:DocumentRequest", "DeleteAny:DocumentRequest",
            "Delete:User",    "DeleteAny:User",
            "Delete:LeaveType", "DeleteAny:LeaveType",
        ]);
        $directeurRole = Role::firstOrCreate(['name' => 'directeur', 'guard_name' => 'web']);
        $directeurRole->syncPermissions(Permission::whereIn('name', $directeurPerms)->get());

        // ─── Permissions communes aux rôles limités ────────────────────────
        $employeePerms = [
            'View:Dashboard',
            'View:MonEspace',
            'ViewAny:Leave',           'View:Leave',           'Create:Leave',
            'ViewAny:DocumentRequest', 'View:DocumentRequest', 'Create:DocumentRequest',
            'View:Employee',           'Update:Employee',
        ];

        // ─── Rôles basic (sans Autres Demandes) ───────────────────────────
        foreach (['femme-de-menage', 'chauffeur', 'gardien'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions(Permission::whereIn('name', $employeePerms)->get());
        }

        // ─── Rôles extended (+ Autres Demandes) ───────────────────────────
        foreach (['enseignant', 'assistante-transport'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions(Permission::whereIn('name', $employeePerms)->get());
        }

        // Supprimer les anciens rôles inutilisés
        Role::whereIn('name', ['admin', 'rh', 'manager', 'comptable', 'enseignante', 'employee'])->delete();

        $this->command->info('Rôles et permissions assignés avec succès.');
        $this->command->table(
            ['Rôle', '# Permissions', 'Accès'],
            [
                ['super-admin',     'Toutes',                              'Plateforme complète + suppression'],
                ['directeur',       $directeurRole->permissions()->count(), 'Gestion complète + suppression soft'],
                ['secretaire',      $secretaireRole->permissions()->count(), 'Dashboard + Congés (gérer) + Docs Admin (gérer) + Autres Demandes (voir)'],
                ['surveillante',    $surveillanteRole->permissions()->count(), 'Dashboard + Congés (voir) + Docs Admin (voir) + Autres Demandes (gérer)'],
                ['femme-de-menage',      count($employeePerms), 'Espace perso, congés, docs'],
                ['chauffeur',            count($employeePerms), 'Espace perso, congés, docs'],
                ['gardien',              count($employeePerms), 'Espace perso, congés, docs'],
                ['enseignant',           count($employeePerms), 'Espace perso, congés, docs + autres demandes'],
                ['assistante-transport', count($employeePerms), 'Espace perso, congés, docs + autres demandes'],
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
