<?php

namespace App\Filament\App\Resources\LeaveResource\Pages;

use App\Filament\App\Resources\LeaveResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\App\Concerns\InjectsCompanyId;

class CreateLeave extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = LeaveResource::class;
}
