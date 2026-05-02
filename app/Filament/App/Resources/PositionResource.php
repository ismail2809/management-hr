<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PositionResource\Pages;
use App\Models\Position;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Postes';
    protected static ?string $modelLabel = 'Poste';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Intitulé')
                ->required()
                ->maxLength(255),

            TextInput::make('base_salary')
                ->label('Salaire de base (MAD)')
                ->numeric()
                ->minValue(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Intitulé')->searchable()->sortable(),
                TextColumn::make('base_salary')->label('Salaire de base')->money('MAD')->sortable(),
                TextColumn::make('employees_count')->label('Employés')->counts('employees')->sortable(),
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
