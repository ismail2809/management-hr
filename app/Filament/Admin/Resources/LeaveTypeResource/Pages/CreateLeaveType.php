<?php

namespace App\Filament\App\Resources\LeaveTypeResource\Pages;

use App\Filament\App\Resources\LeaveTypeResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\App\Concerns\InjectsCompanyId;

class CreateLeaveType extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = LeaveTypeResource::class;
}
