<?php

namespace App\Filament\App\Resources\NatureDocumentResource\Pages;

use App\Filament\App\Resources\NatureDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNatureDocuments extends ListRecords
{
    protected static string $resource = NatureDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
