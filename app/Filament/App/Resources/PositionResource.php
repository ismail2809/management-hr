<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PositionResource\Pages;
use App\Models\Position;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Postes';
    protected static ?string $modelLabel = 'Poste';
    protected static \UnitEnum|string|null $navigationGroup = 'Organisation';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations du poste')->schema([
                TextInput::make('title')
                    ->label('Intitulé du poste')
                    ->required()
                    ->maxLength(255),

                TextInput::make('base_salary')
                    ->label('Salaire de base (MAD)')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->suffix('MAD')
                    ->helperText('Salaire brut de référence pour ce poste'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Intitulé')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('base_salary')
                    ->label('Salaire de base')
                    ->money('MAD')
                    ->sortable(),
                TextColumn::make('employees_count')
                    ->label('Effectif')
                    ->counts('employees')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
            ])
            ->defaultSort('title');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit'   => Pages\EditPosition::route('/{record}/edit'),
        ];
    }
}
