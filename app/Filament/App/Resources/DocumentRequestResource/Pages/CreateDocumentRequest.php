<?php

namespace App\Filament\App\Resources\DocumentRequestResource\Pages;

use App\Filament\App\Resources\DocumentRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentRequest extends CreateRecord
{
    protected static string $resource = DocumentRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
