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

        // ── Créer toutes les permissions métier ────────────────────────────
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

        // Supprimer les permissions parasites (widget/page auto-générées par shield:generate)
        Permission::where('name', 'View:AccountWidget')->delete();

        $employees   = $this->crudPerms('Employee');
        $leaveTypes  = $this->crudPerms('LeaveType');
        $leaves      = $this->crudPerms('Leave');
        $documents   = $this->crudPerms('DocumentRequest');
        $users       = $this->crudPerms('User');
        $auditLogs   = $this->viewOnlyPerms('AuditLog');

        // ─── secretaire ───────────────────────────────────────────────────
        // Docs Admin : viewAny + view + create + update
        // Autres Demandes : viewAny + view (lecture seule — canCreate() refuse secretaire)
        // Congés : gérer
        // Employés + Users : gérer
        $secretairePerms = [
            'View:Dashboard', 'View:MonEspace', 'ApproveLeave',
            'ViewAny:Leave',           'View:Leave',           'Create:Leave',   'Update:Leave',
            'ViewAny:DocumentRequest', 'View:DocumentRequest', 'Create:DocumentRequest', 'Update:DocumentRequest',
            'ViewAny:Employee', 'View:Employee', 'Create:Employee', 'Update:Employee',
            'ViewAny:User',     'View:User',     'Create:User',     'Update:User',
        ];
        $secretaireRole = Role::firstOrCreate(['name' => 'secretaire', 'guard_name' => 'web']);
        $secretaireRole->syncPermissions(Permission::whereIn('name', $secretairePerms)->get());

        // ─── surveillante ─────────────────────────────────────────────────
        // Autres Demandes : viewAny + view + create + update
        // Docs Admin : viewAny + view (lecture seule — canCreate() refuse surveillante)
        // Congés : gérer
        // Employés + Users : voir seulement (canEdit/canCreate sont hardcodés dans le resource)
        $surveillantePerms = [
            'View:Dashboard', 'View:MonEspace', 'ApproveLeave',
            'ViewAny:Leave',           'View:Leave',           'Create:Leave',   'Update:Leave',
            'ViewAny:DocumentRequest', 'View:DocumentRequest', 'Create:DocumentRequest', 'Update:DocumentRequest',
            'ViewAny:Employee', 'View:Employee', 'Create:Employee', 'Update:Employee',
            'ViewAny:User',     'View:User',     'Create:User',     'Update:User',
        ];
        $surveillanteRole = Role::firstOrCreate(['name' => 'surveillante', 'guard_name' => 'web']);
        $surveillanteRole->syncPermissions(Permission::whereIn('name', $surveillantePerms)->get());

        // ─── directeur ────────────────────────────────────────────────────
        // Accès complet (sans Delete — réservé super-admin) + paramétrage
        $directeurPerms = array_values(array_filter(array_merge(
            $employees, $leaveTypes, $leaves,
            $documents, $users, $auditLogs,
            ['ApproveLeave', 'View:HrStatsOverview', 'View:MonEspace', 'View:Dashboard'],
        ), fn ($p) => ! str_starts_with($p, 'Delete')));
        $directeurRole = Role::firstOrCreate(['name' => 'directeur', 'guard_name' => 'web']);
        $directeurRole->syncPermissions(Permission::whereIn('name', $directeurPerms)->get());

        // ─── Permissions communes aux rôles limités ────────────────────────
        // Toutes catégories de DocumentRequest (Docs Admin + Autres Demandes) :
        // l'accès réel à chaque resource est filtré par canViewAny() via isBasicRole()/isExtendedRole()
        $limitedBasePerms = [
            'View:Dashboard', 'View:MonEspace',
            'ViewAny:Leave',           'View:Leave',           'Create:Leave',
            'ViewAny:DocumentRequest', 'View:DocumentRequest', 'Create:DocumentRequest',
            'View:Employee', 'Update:Employee',
        ];

        // Rôles BASIC (femme-de-menage, chauffeur, gardien) :
        // AutreDemandeResource leur est invisible (canViewAny retourne false)
        foreach (['femme-de-menage', 'chauffeur', 'gardien'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions(Permission::whereIn('name', $limitedBasePerms)->get());
        }

        // Rôles EXTENDED (enseignant, assistante-transport) :
        // Mêmes permissions DB mais AutreDemandeResource est accessible via isExtendedRole()
        foreach (['enseignant', 'assistante-transport'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions(Permission::whereIn('name', $limitedBasePerms)->get());
        }

        // Supprimer les anciens rôles inutilisés
        Role::whereIn('name', ['admin', 'rh', 'manager', 'comptable', 'enseignante', 'employee'])->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Rôles et permissions assignés avec succès.');
        $this->command->table(
            ['Rôle', '# Permissions', 'Accès'],
            [
                ['super-admin',          'Toutes (Gate)',                          'Plateforme complète'],
                ['directeur',            $directeurRole->permissions()->count(),   'Gestion complète école (sans suppression)'],
                ['secretaire',           $secretaireRole->permissions()->count(),  'Congés + Docs Admin (CRUD) + Autres Demandes (lecture) + Employés + Users'],
                ['surveillante',         $surveillanteRole->permissions()->count(), 'Congés + Autres Demandes (CRUD) + Docs Admin (lecture) + Employés + Users'],
                ['femme-de-menage',      count($limitedBasePerms),                'Mon espace : congés + docs admin uniquement'],
                ['chauffeur',            count($limitedBasePerms),                'Mon espace : congés + docs admin uniquement'],
                ['gardien',              count($limitedBasePerms),                'Mon espace : congés + docs admin uniquement'],
                ['enseignant',           count($limitedBasePerms),                'Mon espace : congés + docs admin + autres demandes'],
                ['assistante-transport', count($limitedBasePerms),                'Mon espace : congés + docs admin + autres demandes'],
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
