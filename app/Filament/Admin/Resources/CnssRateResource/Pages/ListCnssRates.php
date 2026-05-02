<?php

namespace App\Filament\Admin\Resources\CnssRateResource\Pages;

use App\Filament\Admin\Resources\CnssRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCnssRates extends ListRecords
{
    protected static string $resource = CnssRateResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
