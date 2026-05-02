<?php

namespace App\Filament\App\Resources\LeaveResource\Pages;

use App\Filament\App\Resources\LeaveResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
