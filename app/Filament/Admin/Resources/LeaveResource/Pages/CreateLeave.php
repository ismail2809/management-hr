<?php

namespace App\Filament\Admin\Resources\LeaveResource\Pages;

use App\Filament\Admin\Resources\LeaveResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Concerns\InjectsCompanyId;

class CreateLeave extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = LeaveResource::class;
}
