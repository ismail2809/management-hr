<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\IrBracketResource\Pages;
use App\Models\IrBracket;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IrBracketResource extends Resource
{
    protected static ?string $model = IrBracket::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Barème IR';
    protected static ?string $modelLabel = 'Tranche IR';
    protected static \UnitEnum|string|null $navigationGroup = 'Administration';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super-admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('min_salary')
                ->label('Salaire min (MAD/an)')
                ->numeric()
                ->required(),

            TextInput::make('max_salary')
                ->label('Salaire max (MAD/an)')
                ->numeric()
                ->nullable()
                ->helperText('Laisser vide pour la tranche supérieure sans plafond'),

            TextInput::make('rate_percentage')
                ->label('Taux (%)')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->required(),

            TextInput::make('deduction_amount')
                ->label('Déduction (MAD/an)')
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('min_salary')->label('Min (MAD/an)')->money('MAD')->sortable(),
                TextColumn::make('max_salary')->label('Max (MAD/an)')->money('MAD')->default('Sans plafond')->sortable(),
                TextColumn::make('rate_percentage')->label('Taux')->suffix('%')->sortable(),
                TextColumn::make('deduction_amount')->label('Déduction')->money('MAD'),
            ])
            ->defaultSort('min_salary');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListIrBrackets::route('/'),
            'create' => Pages\CreateIrBracket::route('/create'),
            'edit'   => Pages\EditIrBracket::route('/{record}/edit'),
        ];
    }
}
