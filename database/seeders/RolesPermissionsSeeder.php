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

        $employees   = $this->crudPerms('Employee');
        $leaveTypes  = $this->crudPerms('LeaveType');
        $leaves      = $this->crudPerms('Leave');
        $attendances = $this->crudPerms('Attendance');
        $documents   = $this->crudPerms('DocumentRequest');
        $users       = $this->crudPerms('User');
        $roles       = $this->crudPerms('Role');
        $auditLogs   = $this->viewOnlyPerms('AuditLog');

        // ─── secretaire ────────────────────────────────────────────────────
        // Accès complet à la company (tout sauf gestion des rôles Spatie)
        $secretairePerms = array_merge(
            $employees, $leaveTypes, $leaves, $attendances,
            $documents, $users, $auditLogs,
            ['ApproveLeave', 'View:HrStatsOverview', 'View:MonEspace'],
        );

        $secretaireRole = Role::firstOrCreate(['name' => 'secretaire', 'guard_name' => 'web']);
        $secretaireRole->syncPermissions(Permission::whereIn('name', $secretairePerms)->get());

        // ─── employee ──────────────────────────────────────────────────────
        // Accès limité : son espace, ses congés, ses demandes de documents
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
                ['super-admin', 'Toutes',                              'Plateforme complète'],
                ['secretaire',  $secretaireRole->permissions()->count(), 'Gestion complète de la company'],
                ['employee',    $employeeRole->permissions()->count(),   'Espace perso, congés, demandes'],
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
