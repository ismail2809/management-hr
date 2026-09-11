<?php

namespace App\Filament\Admin\Concerns;

use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;

trait HasCompanyField
{
    public static function companyField(): Section
    {
        $user = \Filament\Facades\Filament::auth()->user();

        $ecoleSettingId = $user?->ecole_setting_id
            ?? \App\Models\EcoleSettings::withoutGlobalScopes()->value('id');

        return Section::make()
            ->hidden()
            ->schema([
                Hidden::make('ecole_setting_id')
                    ->default($ecoleSettingId)
                    ->dehydrated()
                    ->dehydrateStateUsing(fn ($state) => $state
                        ?? \App\Models\EcoleSettings::withoutGlobalScopes()->value('id')),
            ]);
    }
}
