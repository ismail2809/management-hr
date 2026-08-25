<?php

namespace App\Filament\App\Resources\AttendanceResource\Pages;

use App\Filament\App\Resources\AttendanceResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\App\Concerns\InjectsCompanyId;

class CreateAttendance extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = AttendanceResource::class;
}
