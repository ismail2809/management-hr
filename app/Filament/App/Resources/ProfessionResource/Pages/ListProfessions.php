<?php

namespace App\Filament\App\Resources\ProfessionResource\Pages;

use App\Filament\App\Resources\ProfessionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListProfessions extends ListRecords
{
    protected static string $resource = ProfessionResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
