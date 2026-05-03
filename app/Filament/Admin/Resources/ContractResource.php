<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContractResource\Pages;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Contrats';
    protected static ?string $modelLabel = 'Contrat';
    protected static \UnitEnum|string|null $navigationGroup = 'RH';
    protected static ?int $navigationSort = 9;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Entreprise & Employé')->schema([
                Grid::make(2)->schema([
                    Select::make('company_id')
                        ->label('Entreprise')
                        ->options(Company::pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('employee_id', null)),

                    Select::make('employee_id')
                        ->label('Employé')
                        ->options(fn ($get) => Employee::withoutGlobalScopes()
                            ->when($get('company_id'), fn ($q, $cid) => $q->where('company_id', $cid))
                            ->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->required(),
                ]),

                Grid::make(2)->schema([
                    Select::make('contract_type')
                        ->label('Type de contrat')
                        ->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC'])
                        ->required(),

                    Select::make('status')
                        ->label('Statut du contrat')
                        ->options(['actif' => 'Actif', 'termine' => 'Terminé', 'suspendu' => 'Suspendu'])
                        ->required(),
                ]),
            ]),

            Section::make('Période')->schema([
                Grid::make(2)->schema([
                    DatePicker::make('start_date')->label('Date de début')->required(),
                    DatePicker::make('end_date')->label('Date de fin')->nullable()->helperText('Laisser vide pour un CDI'),
                ]),
                DatePicker::make('trial_period_end')->label("Fin de période d'essai")->nullable(),
            ]),

            Section::make('Rémunération & Horaires')->schema([
                Grid::make(2)->schema([
                    TextInput::make('salary_base')->label('Salaire de base')->numeric()->minValue(0)->required()->suffix('MAD'),
                    TextInput::make('working_hours_per_week')->label('Heures par semaine')->numeric()->default(44)->suffix('h'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Entreprise')->sortable()->badge()->color('gray'),
                TextColumn::make('employee.full_name')->label('Employé')->searchable(['employees.first_name', 'employees.last_name'])->sortable()->weight('semibold'),
                TextColumn::make('contract_type')->label('Type')->badge()
                    ->color(fn ($state) => match ($state) {
                        'CDI' => 'success', 'CDD' => 'warning', 'Stage' => 'info', 'ANAPEC' => 'gray', default => 'gray',
                    }),
                TextColumn::make('salary_base')->label('Salaire de base')->money('MAD')->sortable(),
                TextColumn::make('start_date')->label('Début')->date('d/m/Y')->sortable(),
                TextColumn::make('end_date')->label('Fin')->date('d/m/Y')->placeholder('—'),
                TextColumn::make('status')->label('Statut')->badge()
                    ->color(fn ($state) => match ($state) {
                        'actif' => 'success', 'termine' => 'danger', 'suspendu' => 'warning', default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Entreprise')->options(Company::pluck('name', 'id')),
                SelectFilter::make('status')->label('Statut')->options(['actif' => 'Actif', 'termine' => 'Terminé', 'suspendu' => 'Suspendu']),
                SelectFilter::make('contract_type')->label('Type')->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC']),
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
