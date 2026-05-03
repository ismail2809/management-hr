<?php

namespace App\Filament\Admin\Resources\PositionResource\Pages;

use App\Filament\Admin\Resources\PositionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPositions extends ListRecords
{
    protected static string $resource = PositionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
