<?php

namespace App\Filament\App\Resources\Transports\Pages;

use App\Filament\App\Resources\Transports\TransportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransports extends ListRecords
{
    protected static string $resource = TransportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
