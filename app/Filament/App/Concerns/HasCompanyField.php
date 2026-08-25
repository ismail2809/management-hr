<?php

namespace App\Filament\App\Concerns;

use App\Models\Company;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;

trait HasCompanyField
{
    public static function companyField(): Hidden|Select
    {
        $user = \Filament\Facades\Filament::auth()->user();

        if ($user?->hasRole('super-admin')) {
            return Select::make('company_id')
                ->label('Entreprise')
                ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                ->required()
                ->searchable()
                ->columnSpanFull();
        }

        return Hidden::make('company_id')
            ->default($user?->company_id);
    }
}
