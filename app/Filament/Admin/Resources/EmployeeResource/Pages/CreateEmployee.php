<?php

namespace App\Filament\App\Resources\EmployeeResource\Pages;

use App\Filament\App\Resources\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\App\Concerns\InjectsCompanyId;

class CreateEmployee extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = EmployeeResource::class;
}
