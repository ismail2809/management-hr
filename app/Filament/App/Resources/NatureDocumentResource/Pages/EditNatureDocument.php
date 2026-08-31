<?php

namespace App\Filament\App\Resources\NatureDocumentResource\Pages;

use App\Filament\App\Resources\NatureDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNatureDocument extends EditRecord
{
    protected static string $resource = NatureDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
