<?php

namespace App\Filament\App\Resources\Transports\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Transport')->schema([
                \App\Filament\App\Resources\Transports\TransportResource::companyField(),
                TextInput::make('name')->label('Nom')->required()->maxLength(150),
                TextInput::make('matricule')->label('Matricule')->nullable()->maxLength(50),
                Select::make('type')
                    ->label('Type')
                    ->options(['bus' => 'Bus', 'minibus' => 'Minibus', 'voiture' => 'Voiture', 'autre' => 'Autre'])
                    ->required(),
            ]),
        ]);
    }
}
