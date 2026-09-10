<?php

namespace App\Filament\Admin\Resources\EmployeeResource\Pages;

use App\Filament\Admin\Resources\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Concerns\InjectsCompanyId;

class CreateEmployee extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = EmployeeResource::class;
}
