<?php

namespace App\Filament\Admin\Resources\DocumentAdministratifResource\Pages;

use App\Filament\Admin\Resources\DocumentAdministratifResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocumentAdministratifs extends ListRecords
{
    protected static string $resource = DocumentAdministratifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
