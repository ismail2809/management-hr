<?php

namespace App\Filament\App\Concerns;

use App\Models\Company;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;

trait HasCompanyField
{
    public static function companyField(): Section
    {
        $user = \Filament\Facades\Filament::auth()->user();

        if ($user?->hasRole('super-admin')) {
            return Section::make('Ecoles')
                ->icon('heroicon-o-building-office-2')
                ->compact()
                ->schema([
                    Select::make('company_id')
                        ->label('École')
                        ->options(fn () => Company::orderBy('name')->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->columnSpanFull(),
                ]);
        }

        return Section::make()
            ->hidden()
            ->schema([
                Hidden::make('company_id')->default($user?->company_id),
            ]);
    }
}
