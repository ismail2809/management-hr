<?php

namespace App\Filament\App\Resources\DocumentTypes\Pages;

use App\Filament\App\Resources\DocumentTypes\DocumentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentType extends CreateRecord
{
    protected static string $resource = DocumentTypeResource::class;
}
