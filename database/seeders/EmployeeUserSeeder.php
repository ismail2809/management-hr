<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeUserSeeder extends Seeder
{
    /**
     * Mapping profession name → role slug
     */
    private array $professionRoleMap = [
        'Directeur'              => 'directeur',
        'Secrétaire'             => 'secretaire',
        'Surveillant général'    => 'surveillante',
        'Surveillante'           => 'surveillante',
        'Femme de ménage'        => 'femme-de-menage',
        'Chauffeur'              => 'chauffeur',
        'Gardien'                => 'gardien',
        'Assistante de transport'=> 'assistante-transport',
        'Enseignant'             => 'enseignant',
        'Enseignante'            => 'enseignant',
    ];

    /**
     * Corrections d'emails erronés dans la table employees (employee_id => email correct)
     */
    private array $emailCorrections = [
        1 => 'saadia.aabida@lesecolesalbaraime.com', // SAADIA AABIDA avait l'email de HASNAA ABASSI
    ];

    public function run(): void
    {
        $password = Hash::make('Test1234$@#%');
        $usedEmails = [];
        $created = 0;
        $skipped = 0;

        $employees = Employee::withoutGlobalScopes()
            ->with('profession')
            ->whereNotNull('email')
            ->get();

        foreach ($employees as $employee) {
            $email = strtolower(trim($this->emailCorrections[$employee->id] ?? $employee->email));

            // Skip duplicate emails
            if (in_array($email, $usedEmails)) {
                $this->command->warn("  SKIP doublon email [{$employee->id}] {$employee->first_name} {$employee->last_name} → {$email}");
                $skipped++;
                continue;
            }

            $professionName = $employee->profession?->name;
            $roleName = $this->professionRoleMap[$professionName] ?? null;

            if (! $roleName) {
                $this->command->warn("  SKIP profession inconnue [{$employee->id}] {$employee->first_name} {$employee->last_name} → profession: {$professionName}");
                $skipped++;
                continue;
            }

            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                $this->command->warn("  SKIP rôle introuvable [{$employee->id}] → role: {$roleName}");
                $skipped++;
                continue;
            }

            $user = User::withoutGlobalScopes()->updateOrCreate(
                ['email' => $email],
                [
                    'name'             => trim($employee->first_name . ' ' . $employee->last_name),
                    'password'         => $password,
                    'ecole_setting_id' => $employee->ecole_setting_id,
                    'employee_id'      => $employee->id,
                ]
            );

            $user->syncRoles([$role->name]);

            $usedEmails[] = $email;
            $created++;

            $this->command->info("  OK [{$employee->id}] {$employee->first_name} {$employee->last_name} → {$email} [{$roleName}]");
        }

        $this->command->newLine();
        $this->command->info("Terminé : {$created} comptes créés/mis à jour, {$skipped} ignorés.");
    }
}
