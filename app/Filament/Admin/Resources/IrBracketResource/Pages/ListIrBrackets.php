<?php

namespace App\Filament\Admin\Resources\IrBracketResource\Pages;

use App\Filament\Admin\Resources\IrBracketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIrBrackets extends ListRecords
{
    protected static string $resource = IrBracketResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
