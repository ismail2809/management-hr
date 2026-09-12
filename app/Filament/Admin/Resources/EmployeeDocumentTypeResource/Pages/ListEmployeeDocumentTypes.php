<?php

namespace App\Filament\Admin\Resources\EmployeeDocumentTypeResource\Pages;

use App\Filament\Admin\Resources\EmployeeDocumentTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeDocumentTypes extends ListRecords
{
    protected static string $resource = EmployeeDocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
