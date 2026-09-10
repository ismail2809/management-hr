<?php

namespace App\Filament\Admin\Resources\DocumentAdministratifResource\Pages;

use App\Filament\Admin\Resources\DocumentAdministratifResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDocumentAdministratif extends EditRecord
{
    protected static string $resource = DocumentAdministratifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
