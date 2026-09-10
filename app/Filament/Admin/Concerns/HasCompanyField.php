<?php

namespace App\Filament\App\Concerns;

use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;

trait HasCompanyField
{
    public static function companyField(): Section
    {
        $user = \Filament\Facades\Filament::auth()->user();

        $companyId = $user?->company_id
            ?? \App\Models\Company::value('id');

        return Section::make()
            ->hidden()
            ->schema([
                Hidden::make('company_id')->default($companyId),
            ]);
    }
}
