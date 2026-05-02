<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ContractResource\Pages;
use App\Models\Contract;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Contrats';
    protected static ?string $modelLabel = 'Contrat';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('employee_id')
                ->label('Employé')
                ->relationship('employee', 'first_name')
                ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                ->searchable()
                ->default(fn () => auth()->user()?->employee_id)
                ->required(),

            Grid::make(2)->schema([
                Select::make('contract_type')
                    ->label('Type de contrat')
                    ->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC'])
                    ->required(),

                Select::make('status')
                    ->label('Statut')
                    ->options(['actif' => 'Actif', 'termine' => 'Terminé', 'suspendu' => 'Suspendu'])
                    ->required(),
            ]),

            Grid::make(2)->schema([
                DatePicker::make('start_date')->label('Début')->required(),
                DatePicker::make('end_date')->label('Fin')->nullable(),
            ]),

            Grid::make(2)->schema([
                TextInput::make('salary_base')
                    ->label('Salaire de base (MAD)')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                TextInput::make('working_hours_per_week')
                    ->label('Heures/semaine')
                    ->numeric()
                    ->default(44),
            ]),

            DatePicker::make('trial_period_end')->label("Fin période d'essai")->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')->label('Employé')->searchable(['employees.first_name', 'employees.last_name'])->sortable(),
                TextColumn::make('contract_type')->label('Type')->badge()
                    ->color(fn ($state) => match ($state) {
                        'CDI'    => 'success',
                        'CDD'    => 'warning',
                        'Stage'  => 'info',
                        'ANAPEC' => 'gray',
                        default  => 'gray',
                    }),
                TextColumn::make('salary_base')->label('Salaire base')->money('MAD')->sortable(),
                TextColumn::make('start_date')->label('Début')->date('d/m/Y')->sortable(),
                TextColumn::make('end_date')->label('Fin')->date('d/m/Y')->placeholder('—'),
                TextColumn::make('status')->label('Statut')->badge()
                    ->color(fn ($state) => match ($state) {
                        'actif'    => 'success',
                        'termine'  => 'danger',
                        'suspendu' => 'warning',
                        default    => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(['actif' => 'Actif', 'termine' => 'Terminé', 'suspendu' => 'Suspendu']),
                SelectFilter::make('contract_type')
                    ->label('Type')
                    ->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC']),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit'   => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
