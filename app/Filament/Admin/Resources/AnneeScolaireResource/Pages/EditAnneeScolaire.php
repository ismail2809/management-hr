<?php

namespace App\Filament\Admin\Resources\AnneeScolaireResource\Pages;

use App\Filament\Admin\Resources\AnneeScolaireResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnneeScolaire extends EditRecord
{
    protected static string $resource = AnneeScolaireResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
