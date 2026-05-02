<?php

namespace App\Filament\App\Resources\DeclarationResource\Pages;

use App\Filament\App\Resources\DeclarationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeclarations extends ListRecords
{
    protected static string $resource = DeclarationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
