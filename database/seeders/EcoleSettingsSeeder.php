<?php

namespace Database\Seeders;

use App\Models\EcoleSettings;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class EcoleSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Supprimer les anciens rôles inutilisés
        Role::whereIn('name', ['admin', 'rh', 'manager', 'comptable'])->delete();

        // Créer les rôles valides
        foreach (['super-admin', 'directeur', 'secretaire', 'surveillante', 'employee'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Créer le paramétrage de l'école
        $ecole = EcoleSettings::firstOrCreate(
            ['nom_ecole' => 'Les écoles AL BARAIME'],
            [
                'cnss'                => '2920222',
                'patente'             => '42500016',
                'rc'                  => '16320',
                'if_number'           => '64400070',
                'adresse'             => 'Bvd Mohammed V, Bp 14, Azemmour, 24100 Maroc',
                'ville'               => 'Azemmour',
                'code_postal'         => '24100',
                'pays'                => 'Maroc',
                'telephone'           => '+212523358346',
                'email'               => 'albaraime.viescholaire@gmail.com',
                'site_web'            => 'https://lesecolesalbaraime.com/',
                'logo'                => 'ecole/01M29J1HDNYV8Y3BNVJ5MS1HMC.png',
                'afficher_logo_pdf'   => true,
                'afficher_cachet_pdf' => true,
            ]
        );

        // Créer un utilisateur secretaire de test lié à cette école
        $secretaireRole = Role::where('name', 'secretaire')->first();

        $user = User::updateOrCreate(
            ['email' => 'secretaire@albaraime.ma'],
            [
                'name'             => 'Secrétaire Test',
                'password'         => bcrypt('password'),
                'ecole_setting_id' => $ecole->id,
            ]
        );

        $user->syncRoles([$secretaireRole]);

        $this->command->info("École créée : {$ecole->nom_ecole} (ID: {$ecole->id})");
    }
}
