<?php

namespace App\Filament\App\Resources\LeaveResource\Pages;

use App\Filament\App\Resources\LeaveResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeave extends EditRecord
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
