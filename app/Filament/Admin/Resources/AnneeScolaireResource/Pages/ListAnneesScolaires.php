<?php

namespace App\Filament\Admin\Resources\AnneeScolaireResource\Pages;

use App\Filament\Admin\Resources\AnneeScolaireResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnneesScolaires extends ListRecords
{
    protected static string $resource = AnneeScolaireResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
