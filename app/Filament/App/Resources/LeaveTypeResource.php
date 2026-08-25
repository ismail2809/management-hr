<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\LeaveTypeResource\Pages;
use App\Models\LeaveType;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeaveTypeResource extends Resource
{
    use HasCompanyField;
    protected static ?string $model = LeaveType::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Types de congé';
    protected static ?string $modelLabel = 'Type de congé';
    protected static \UnitEnum|string|null $navigationGroup = 'Congés & Présence';
    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Type de congé')->schema([
                static::companyField(),

                TextInput::make('name')
                    ->label('Nom')
                    ->placeholder('Annuel, Maladie, Maternité, Sans solde…')
                    ->required()
                    ->maxLength(100),

                TextInput::make('legal_days_per_year')
                    ->label('Jours légaux par an')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required()
                    ->suffix('jours'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('legal_days_per_year')
                    ->label('Jours / an')
                    ->suffix(' j')
                    ->sortable(),
                TextColumn::make('leaves_count')
                    ->label('Demandes')
                    ->counts('leaves')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function canViewAny(): bool
    {
        return ! auth()->user()?->hasRole('employee');
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
