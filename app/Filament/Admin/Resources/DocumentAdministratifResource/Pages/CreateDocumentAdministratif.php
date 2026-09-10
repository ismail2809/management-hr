<?php

namespace App\Filament\App\Resources\DocumentAdministratifResource\Pages;

use App\Filament\App\Resources\DocumentAdministratifResource;
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
        if (empty($data['company_id'])) {
            $data['company_id'] = $user?->company_id;
        }

        return $data;
    }
}
