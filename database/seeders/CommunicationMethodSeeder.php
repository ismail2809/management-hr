<?php

namespace Database\Seeders;

use App\Models\CommunicationMethod;
use Illuminate\Database\Seeder;

class CommunicationMethodSeeder extends Seeder
{
    public function run(): void
    {
        // Méthodes globales (company_id = null) — disponibles pour toutes les companies
        $methods = [
            ['code' => 'email',       'name' => 'Email',       'sort_order' => 1],
            ['code' => 'telephone',   'name' => 'Téléphone',   'sort_order' => 2],
            ['code' => 'whatsapp',    'name' => 'WhatsApp',    'sort_order' => 3],
            ['code' => 'sms',         'name' => 'SMS',         'sort_order' => 4],
            ['code' => 'courrier',    'name' => 'Courrier',    'sort_order' => 5],
            ['code' => 'presentiel',  'name' => 'Présentiel',  'sort_order' => 6],
        ];

        foreach ($methods as $data) {
            CommunicationMethod::withoutGlobalScopes()->firstOrCreate(
                ['code' => $data['code'], 'company_id' => null],
                array_merge($data, ['active' => true])
            );
        }

        $this->command->info('Méthodes de communication seedées : ' . count($methods));
    }
}
