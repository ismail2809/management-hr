<?php

namespace App\Filament\App\Resources\AutreDemandeResource\Pages;

use App\Filament\App\Resources\AutreDemandeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAutreDemande extends CreateRecord
{
    protected static string $resource = AutreDemandeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['categorie'] = 'autre';

        $user = \Filament\Facades\Filament::auth()->user();
        if (empty($data['company_id'])) {
            $data['company_id'] = $user?->company_id;
        }

        return $data;
    }
}
