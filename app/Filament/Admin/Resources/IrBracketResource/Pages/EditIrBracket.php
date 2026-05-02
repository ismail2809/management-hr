<?php

namespace App\Filament\Admin\Resources\IrBracketResource\Pages;

use App\Filament\Admin\Resources\IrBracketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIrBracket extends EditRecord
{
    protected static string $resource = IrBracketResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
