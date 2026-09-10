<?php

namespace App\Filament\Admin\Resources\CommunicationMethods\Pages;

use App\Filament\Admin\Resources\CommunicationMethods\CommunicationMethodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommunicationMethods extends ListRecords
{
    protected static string $resource = CommunicationMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
