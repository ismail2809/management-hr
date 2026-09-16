<?php

namespace App\Filament\Admin\Resources\AnneeScolaireResource\Pages;

use App\Filament\Admin\Concerns\InjectsCompanyId;
use App\Filament\Admin\Resources\AnneeScolaireResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnneeScolaire extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = AnneeScolaireResource::class;
}
