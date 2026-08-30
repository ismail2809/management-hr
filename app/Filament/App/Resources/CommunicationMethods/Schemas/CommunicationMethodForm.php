<?php

namespace App\Filament\App\Resources\CommunicationMethods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput as NumberInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommunicationMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Mode de communication')->schema([
                TextInput::make('name')
                    ->label('Nom')
                    ->placeholder('Téléphone, Email, WhatsApp…')
                    ->required()
                    ->maxLength(100),

                TextInput::make('code')
                    ->label('Code (identifiant unique)')
                    ->placeholder('telephone, email, whatsapp…')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                TextInput::make('sort_order')
                    ->label('Ordre d\'affichage')
                    ->numeric()
                    ->default(0),

                Toggle::make('active')
                    ->label('Actif')
                    ->default(true),
            ]),
        ]);
    }
}
