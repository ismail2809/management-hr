<?php

namespace App\Filament\App\Resources\Transports\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Transport')->schema([
                \App\Filament\App\Resources\Transports\TransportResource::companyField(),
                Grid::make(2)->schema([
                    TextInput::make('name')->label('Nom')->required()->maxLength(150),
                    TextInput::make('matricule')->label('Matricule')->nullable()->maxLength(50),
                ]),
            ]),
        ]);
    }
}
