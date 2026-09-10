<?php

namespace App\Filament\Admin\Resources\NatureDocumentResource\Pages;

use App\Filament\Admin\Concerns\InjectsCompanyId;
use App\Filament\Admin\Resources\NatureDocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNatureDocument extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = NatureDocumentResource::class;
}
