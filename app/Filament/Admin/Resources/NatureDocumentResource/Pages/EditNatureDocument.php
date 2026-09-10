<?php

namespace App\Filament\Admin\Resources\NatureDocumentResource\Pages;

use App\Filament\Admin\Resources\NatureDocumentResource;
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
