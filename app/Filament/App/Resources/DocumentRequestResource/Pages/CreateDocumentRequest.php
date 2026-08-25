<?php

namespace App\Filament\App\Resources\DocumentRequestResource\Pages;

use App\Filament\App\Resources\DocumentRequestResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\App\Concerns\InjectsCompanyId;

class CreateDocumentRequest extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = DocumentRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
