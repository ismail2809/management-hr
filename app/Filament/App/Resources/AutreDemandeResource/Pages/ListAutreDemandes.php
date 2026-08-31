<?php

namespace App\Filament\App\Resources\AutreDemandeResource\Pages;

use App\Filament\App\Resources\AutreDemandeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutreDemandes extends ListRecords
{
    protected static string $resource = AutreDemandeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
