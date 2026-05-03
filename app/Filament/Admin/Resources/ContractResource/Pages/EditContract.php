<?php

namespace App\Filament\Admin\Resources\ContractResource\Pages;

use App\Filament\Admin\Resources\ContractResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
