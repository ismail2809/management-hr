<?php

namespace App\Filament\Admin\Resources\DocumentAdministratifResource\Pages;

use App\Filament\Admin\Resources\DocumentAdministratifResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentAdministratif extends CreateRecord
{
    protected static string $resource = DocumentAdministratifResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['categorie'] = 'document';

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
