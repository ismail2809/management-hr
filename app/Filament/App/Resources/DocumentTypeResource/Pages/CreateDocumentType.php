<?php

namespace App\Filament\App\Resources\DocumentTypeResource\Pages;

use App\Filament\App\Concerns\InjectsCompanyId;
use App\Filament\App\Resources\DocumentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentType extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = DocumentTypeResource::class;
}
