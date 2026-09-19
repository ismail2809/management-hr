<?php

namespace App\Filament\Admin\Resources\AutreDemandeResource\Pages;

use App\Filament\Admin\Resources\AutreDemandeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAutreDemande extends CreateRecord
{
    protected static string $resource = AutreDemandeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        AutreDemandeResource::checkPhotocopieConflict($this->form->getState());
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['categorie'] = 'autre';

        $user = \Filament\Facades\Filament::auth()->user();
        if (empty($data['ecole_setting_id'])) {
            $data['ecole_setting_id'] = $user?->ecole_setting_id;
        }

        if ($user?->isBasicRole()) {
            $data['employee_id'] = $user->employee_id;
            $data['status']      = 'en_attente';
        }

        return $data;
    }
}
