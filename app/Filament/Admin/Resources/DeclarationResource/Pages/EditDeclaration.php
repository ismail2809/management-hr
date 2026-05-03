<?php

namespace App\Filament\Admin\Resources\DeclarationResource\Pages;

use App\Filament\Admin\Resources\DeclarationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeclaration extends EditRecord
{
    protected static string $resource = DeclarationResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
