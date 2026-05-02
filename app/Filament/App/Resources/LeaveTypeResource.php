<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\LeaveTypeResource\Pages;
use App\Models\LeaveType;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveTypeResource extends Resource
{
    protected static ?string $model = LeaveType::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Types de congé';
    protected static ?string $modelLabel = 'Type de congé';
    protected static \UnitEnum|string|null $navigationGroup = 'Congés';
    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nom')
                ->placeholder('Annuel, Maladie, Maternité, Sans solde…')
                ->required()
                ->maxLength(100),

            TextInput::make('legal_days_per_year')
                ->label('Jours légaux / an')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('legal_days_per_year')->label('Jours / an')->sortable(),
                TextColumn::make('leaves_count')->label('Demandes')->counts('leaves')->sortable(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaveTypes::route('/'),
            'create' => Pages\CreateLeaveType::route('/create'),
            'edit'   => Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }
}
