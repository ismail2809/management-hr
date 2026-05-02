<?php

namespace App\Filament\Admin\Resources\CnssRateResource\Pages;

use App\Filament\Admin\Resources\CnssRateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCnssRate extends EditRecord
{
    protected static string $resource = CnssRateResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
