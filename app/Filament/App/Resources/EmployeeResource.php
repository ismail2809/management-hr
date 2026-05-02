<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Employés';
    protected static ?string $modelLabel = 'Employé';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identité')->schema([
                Grid::make(3)->schema([
                    TextInput::make('matricule')->label('Matricule')->maxLength(50),
                    TextInput::make('first_name')->label('Prénom')->required()->maxLength(100),
                    TextInput::make('last_name')->label('Nom')->required()->maxLength(100),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('cin')->label('CIN')->maxLength(20),
                    TextInput::make('cnss_number')->label('N° CNSS')->maxLength(30),
                    TextInput::make('rib')->label('RIB')->maxLength(30),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('email')->label('Email')->email()->nullable(),
                    TextInput::make('phone')->label('Téléphone')->nullable(),
                ]),
                Grid::make(2)->schema([
                    DatePicker::make('birth_date')->label('Date de naissance')->nullable(),
                    DatePicker::make('hire_date')->label("Date d'embauche")->nullable(),
                ]),
            ]),

            Section::make('Situation familiale')->schema([
                Grid::make(2)->schema([
                    Select::make('marital_status')
                        ->label('Situation familiale')
                        ->options([
                            'celibataire' => 'Célibataire',
                            'marie'       => 'Marié(e)',
                            'divorce'     => 'Divorcé(e)',
                            'veuf'        => 'Veuf/Veuve',
                        ])
                        ->required(),

                    TextInput::make('number_of_children')
                        ->label("Nombre d'enfants")
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                ]),
            ]),

            Section::make('Contrat & Affectation')->schema([
                Grid::make(2)->schema([
                    Select::make('contract_type')
                        ->label('Type de contrat')
                        ->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC'])
                        ->required(),

                    Select::make('status')
                        ->label('Statut')
                        ->options(['actif' => 'Actif', 'inactif' => 'Inactif', 'suspendu' => 'Suspendu'])
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    Select::make('department_id')
                        ->label('Département')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->nullable(),

                    Select::make('position_id')
                        ->label('Poste')
                        ->relationship('position', 'title')
                        ->searchable()
                        ->nullable(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('matricule')->label('Matricule')->searchable()->sortable(),
                TextColumn::make('full_name')->label('Nom complet')->searchable(['first_name', 'last_name'])->sortable('last_name'),
                TextColumn::make('department.name')->label('Département')->sortable()->default('—'),
                TextColumn::make('position.title')->label('Poste')->sortable()->default('—'),
                TextColumn::make('contract_type')->label('Contrat')->badge()
                    ->color(fn ($state) => match ($state) {
                        'CDI'    => 'success',
                        'CDD'    => 'warning',
                        'Stage'  => 'info',
                        'ANAPEC' => 'gray',
                        default  => 'gray',
                    }),
                TextColumn::make('status')->label('Statut')->badge()
                    ->color(fn ($state) => match ($state) {
                        'actif'    => 'success',
                        'inactif'  => 'danger',
                        'suspendu' => 'warning',
                        default    => 'gray',
                    }),
                TextColumn::make('hire_date')->label('Embauche')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(['actif' => 'Actif', 'inactif' => 'Inactif', 'suspendu' => 'Suspendu']),
                SelectFilter::make('contract_type')
                    ->label('Type contrat')
                    ->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC']),
                SelectFilter::make('department_id')
                    ->label('Département')
                    ->relationship('department', 'name'),
            ])
            ->defaultSort('last_name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit'   => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
