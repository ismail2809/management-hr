<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove permissions that are never triggered by any UI action in this app:
     *  - Restore / RestoreAny / ForceDelete / ForceDeleteAny / Replicate / Reorder (all resources)
     *  - Auto-generated widget permissions (visibility is handled directly in canView())
     *  - Auto-generated page permission for EcoleSettings (canAccess() uses role check)
     *  - Stale payroll permissions left over from the old SaaS payroll module
     */
    public function up(): void
    {
        $unusedPrefixes = [
            'Restore:%',
            'RestoreAny:%',
            'ForceDelete:%',
            'ForceDeleteAny:%',
            'Replicate:%',
            'Reorder:%',
        ];

        foreach ($unusedPrefixes as $pattern) {
            DB::table('permissions')->where('name', 'like', $pattern)->delete();
        }

        // Widget permissions — canView() is hardcoded per widget, Shield has no role here
        $unusedExact = [
            'View:AccountWidget',
            'View:AutreDemandesWidget',
            'View:DocumentsAdministratifsWidget',
            'View:EmployeeStatsWidget',
            'View:EnseignantAutreDemandesWidget',
            'View:EnseignantDemandesWidget',
            'View:EnseignantStatsWidget',
            'View:LeavesWidget',
            // Page permission — EcoleSettings.canAccess() uses hasRole() directly
            'View:EcoleSettings',
            // Stale payroll permissions (module was removed)
            'MarkPayrollPaid',
            'ValidatePayroll',
        ];

        DB::table('permissions')->whereIn('name', $unusedExact)->delete();

        // Clear Spatie permission cache
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Permissions are recreated by: php artisan shield:generate --all --panel=app
    }
};
