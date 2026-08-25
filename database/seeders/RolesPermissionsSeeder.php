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

        $customLeave = ['ApproveLeave'];

        $auditLogs = $this->viewOnlyPerms('AuditLog');

        // ─── admin ─────────────────────────────────────────────────────────
        $adminPerms = array_merge(
            $employees, $leaveTypes, $leaves, $attendances,
            $documents, $users, $roles, $auditLogs,
            $customLeave,
            ['View:HrStatsOverview', 'View:EmployeeStatsWidget', 'View:MonEspace'],
        );

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::whereIn('name', $adminPerms)->get());

        // ─── rh ────────────────────────────────────────────────────────────
        $rhPerms = array_merge(
            $employees, $leaveTypes, $leaves, $attendances, $documents,
            $customLeave,
            ['View:HrStatsOverview', 'View:EmployeeStatsWidget'],
        );

        $rhRole = Role::firstOrCreate(['name' => 'rh', 'guard_name' => 'web']);
        $rhRole->syncPermissions(Permission::whereIn('name', $rhPerms)->get());

        // ─── manager ───────────────────────────────────────────────────────
        $managerPerms = array_merge(
            $this->viewOnlyPerms('Employee'),
            $this->viewOnlyPerms('Leave'),
            $this->viewOnlyPerms('Attendance'),
            $customLeave,
        );

        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions(Permission::whereIn('name', $managerPerms)->get());

        // ─── comptable ─────────────────────────────────────────────────────
        $comptablePerms = array_merge(
            $this->viewOnlyPerms('Employee'),
            ['View:HrStatsOverview'],
        );

        $comptableRole = Role::firstOrCreate(['name' => 'comptable', 'guard_name' => 'web']);
        $comptableRole->syncPermissions(Permission::whereIn('name', $comptablePerms)->get());

        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        $this->command->info('Rôles et permissions assignés avec succès.');
        $this->command->table(
            ['Rôle', '# Permissions'],
            [
                ['admin',     $adminRole->permissions()->count()],
                ['rh',        $rhRole->permissions()->count()],
                ['manager',   $managerRole->permissions()->count()],
                ['comptable', $comptableRole->permissions()->count()],
                ['employee',  $employeeRole->permissions()->count()],
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
