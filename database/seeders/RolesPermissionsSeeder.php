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
        // Reset cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions par module (format Shield PascalCase)
        $employees  = $this->crudPerms('Employee');
        $contracts  = $this->crudPerms('Contract');
        $departments = $this->crudPerms('Department');
        $positions  = $this->crudPerms('Position');
        $leaveTypes = $this->crudPerms('LeaveType');
        $leaves     = $this->crudPerms('Leave');
        $attendances = $this->crudPerms('Attendance');
        $payrolls   = $this->crudPerms('Payroll');
        $declarations = $this->crudPerms('Declaration');
        $documents  = $this->crudPerms('DocumentRequest');
        $users      = $this->crudPerms('User');
        $roles      = $this->crudPerms('Role');

        // Permissions personnalisées
        $customLeave   = ['ApproveLeave'];
        $customPayroll = ['ValidatePayroll', 'MarkPayrollPaid'];

        // ─── admin (company admin) ──────────────────────────────────────────
        // Accès complet à tous les modules RH de sa company
        $auditLogs = $this->viewOnlyPerms('AuditLog');

        $adminPerms = array_merge(
            $employees, $contracts, $departments, $positions,
            $leaveTypes, $leaves, $attendances,
            $payrolls, $declarations, $documents,
            $users, $roles, $auditLogs,
            $customLeave, $customPayroll,
            ['View:HrStatsOverview', 'View:PayrollChartWidget', 'View:EmployeeStatsWidget', 'View:GeneratePayroll', 'View:MonEspace', 'View:MonEspace'],
        );
        // admin can manage users (create/edit/delete)
        // already included via $users = crudPerms('User')

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(
            Permission::whereIn('name', $adminPerms)->get()
        );

        // ─── rh ────────────────────────────────────────────────────────────
        // Gestion employés, contrats, présence, congés, documents
        // Paie : lecture seule
        $rhPerms = array_merge(
            $employees, $contracts, $departments, $positions,
            $leaveTypes, $leaves, $attendances, $documents,
            $customLeave,
            $this->viewOnlyPerms('Payroll'),
            ['View:HrStatsOverview', 'View:EmployeeStatsWidget'],
        );

        $rhRole = Role::firstOrCreate(['name' => 'rh', 'guard_name' => 'web']);
        $rhRole->syncPermissions(
            Permission::whereIn('name', $rhPerms)->get()
        );

        // ─── manager ───────────────────────────────────────────────────────
        // Vue employés, validation congés, vue présence
        $managerPerms = array_merge(
            $this->viewOnlyPerms('Employee'),
            $this->viewOnlyPerms('Leave'),
            $this->viewOnlyPerms('Attendance'),
            $customLeave,
        );

        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions(
            Permission::whereIn('name', $managerPerms)->get()
        );

        // ─── comptable ─────────────────────────────────────────────────────
        // Paie complète + déclarations + vue employés
        $comptablePerms = array_merge(
            $payrolls, $declarations,
            $this->viewOnlyPerms('Employee'),
            $customPayroll,
            ['View:HrStatsOverview', 'View:PayrollChartWidget', 'View:GeneratePayroll'],
        );

        $comptableRole = Role::firstOrCreate(['name' => 'comptable', 'guard_name' => 'web']);
        $comptableRole->syncPermissions(
            Permission::whereIn('name', $comptablePerms)->get()
        );

        $this->command->info('Rôles et permissions assignés avec succès.');
        $this->command->table(
            ['Rôle', '# Permissions'],
            [
                ['admin',     $adminRole->permissions()->count()],
                ['rh',        $rhRole->permissions()->count()],
                ['manager',   $managerRole->permissions()->count()],
                ['comptable', $comptableRole->permissions()->count()],
            ]
        );
    }

    /** CRUD complet : ViewAny, View, Create, Update, Delete, DeleteAny */
    private function crudPerms(string $model): array
    {
        return [
            "ViewAny:{$model}", "View:{$model}",
            "Create:{$model}", "Update:{$model}",
            "Delete:{$model}", "DeleteAny:{$model}",
        ];
    }

    /** Lecture seule : ViewAny + View */
    private function viewOnlyPerms(string $model): array
    {
        return ["ViewAny:{$model}", "View:{$model}"];
    }
}
