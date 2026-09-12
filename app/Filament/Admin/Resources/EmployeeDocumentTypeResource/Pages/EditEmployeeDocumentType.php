<?php

namespace App\Filament\Admin\Resources\EmployeeDocumentTypeResource\Pages;

use App\Filament\Admin\Resources\EmployeeDocumentTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeDocumentType extends EditRecord
{
    protected static string $resource = EmployeeDocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
