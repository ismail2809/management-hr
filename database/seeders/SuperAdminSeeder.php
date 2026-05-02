<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Créer le rôle super-admin s'il n'existe pas
        $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        // Créer ou mettre à jour le super-admin
        $user = User::updateOrCreate(
            ['email' => 'admin@gestion-hr.ma'],
            [
                'name'       => 'Super Admin',
                'password'   => bcrypt('password'),
                'company_id' => null,
            ]
        );

        $user->syncRoles([$role]);
    }
}
