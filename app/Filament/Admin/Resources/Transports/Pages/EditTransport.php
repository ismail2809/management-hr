<?php

namespace App\Filament\Admin\Resources\Transports\Pages;

use App\Filament\Admin\Resources\Transports\TransportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTransport extends EditRecord
{
    protected static string $resource = TransportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
