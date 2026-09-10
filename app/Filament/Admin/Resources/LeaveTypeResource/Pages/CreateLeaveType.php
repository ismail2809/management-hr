<?php

namespace App\Filament\Admin\Resources\LeaveTypeResource\Pages;

use App\Filament\Admin\Resources\LeaveTypeResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Concerns\InjectsCompanyId;

class CreateLeaveType extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = LeaveTypeResource::class;
}
