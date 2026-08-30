<?php

namespace App\Filament\App\Resources\CommunicationMethods\Pages;

use App\Filament\App\Resources\CommunicationMethods\CommunicationMethodResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCommunicationMethod extends EditRecord
{
    protected static string $resource = CommunicationMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
