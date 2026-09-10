<?php

namespace App\Filament\App\Resources\NatureDocumentResource\Pages;

use App\Filament\App\Concerns\InjectsCompanyId;
use App\Filament\App\Resources\NatureDocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNatureDocument extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = NatureDocumentResource::class;
}
