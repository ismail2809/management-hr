<?php

namespace App\Filament\App\Resources\DocumentTypes\Schemas;

use App\Filament\App\Concerns\HasCompanyField;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DocumentTypeForm
{
    use HasCompanyField;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                static::companyField(),

                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(100)
                    ->live(debounce: 500)
                    ->afterStateUpdated(fn ($state, $set) => $set('code', $state ? \Illuminate\Support\Str::slug($state, '_') : null)),

                TextInput::make('code')
                    ->label('Code (template PDF)')
                    ->helperText('Généré automatiquement depuis le nom — modifiable manuellement')
                    ->nullable()
                    ->maxLength(100),

                Select::make('categorie')
                    ->label('Catégorie')
                    ->options(['document' => 'Document administratif', 'autre' => 'Autre demande'])
                    ->default('document')
                    ->required(),

                TextInput::make('sort_order')
                    ->label("Ordre d'affichage")
                    ->numeric()
                    ->default(0),

                Toggle::make('active')
                    ->label('Actif')
                    ->default(true),
            ]);
    }
}
