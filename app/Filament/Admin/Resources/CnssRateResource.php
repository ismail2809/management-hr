<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CnssRateResource\Pages;
use App\Models\CnssRate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CnssRateResource extends Resource
{
    protected static ?string $model = CnssRate::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-percent-badge';
    protected static ?string $navigationLabel = 'Taux CNSS/AMO';
    protected static ?string $modelLabel = 'Taux';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramètres légaux';

    // Le super-admin voit TOUS les taux (globaux + toutes companies)
    public static function getEloquentQuery(): Builder
    {
        return CnssRate::withoutGlobalScopes();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label('Type')
                ->options(['employee' => 'Salarié', 'employer' => 'Patronal'])
                ->required(),

            TextInput::make('label')
                ->label('Libellé')
                ->placeholder('CNSS, AMO, CIMR, Formation…')
                ->required(),

            TextInput::make('rate_percentage')
                ->label('Taux (%)')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->required(),

            TextInput::make('plafond')
                ->label('Plafond mensuel (MAD)')
                ->numeric()
                ->nullable()
                ->helperText('Laisser vide = pas de plafond'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('Libellé')->searchable()->sortable(),
                TextColumn::make('type')->label('Type')->badge()
                    ->color(fn ($state) => $state === 'employee' ? 'info' : 'warning'),
                TextColumn::make('rate_percentage')->label('Taux')->suffix('%')->sortable(),
                TextColumn::make('plafond')->label('Plafond/mois')->money('MAD')->default('—'),
                TextColumn::make('company.name')->label('Company')->default('Global'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(['employee' => 'Salarié', 'employer' => 'Patronal']),
            ])
            ->defaultSort('type');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCnssRates::route('/'),
            'create' => Pages\CreateCnssRate::route('/create'),
            'edit'   => Pages\EditCnssRate::route('/{record}/edit'),
        ];
    }
}
