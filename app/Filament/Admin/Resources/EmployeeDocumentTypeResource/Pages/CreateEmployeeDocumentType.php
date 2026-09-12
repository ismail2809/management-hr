<?php

namespace App\Filament\Admin\Resources\EmployeeDocumentTypeResource\Pages;

use App\Filament\Admin\Concerns\InjectsCompanyId;
use App\Filament\Admin\Resources\EmployeeDocumentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeDocumentType extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = EmployeeDocumentTypeResource::class;
}
