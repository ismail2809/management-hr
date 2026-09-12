<?php

namespace App\Filament\Admin\Resources\DocumentTypeResource\Pages;

use App\Filament\Admin\Concerns\InjectsCompanyId;
use App\Filament\Admin\Resources\DocumentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentType extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = DocumentTypeResource::class;
}
