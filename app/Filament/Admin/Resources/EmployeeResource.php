<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmployeeResource\Pages;
use App\Models\Company;
use App\Models\Employee;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Employés';
    protected static ?string $modelLabel = 'Employé';
    protected static \UnitEnum|string|null $navigationGroup = 'RH';
    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Tabs::make()->tabs([

                Tab::make('Informations personnelles')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Select::make('company_id')
                            ->label('Entreprise')
                            ->options(Company::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Grid::make(3)->schema([
                            TextInput::make('matricule')
                                ->label('Matricule')
                                ->placeholder('Auto-généré si vide')
                                ->helperText('Laissez vide pour génération automatique')
                                ->maxLength(50),
                            TextInput::make('first_name')->label('Prénom')->required()->maxLength(100),
                            TextInput::make('last_name')->label('Nom')->required()->maxLength(100),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('cin')->label('CIN')->maxLength(20),
                            TextInput::make('cnss_number')->label('N° CNSS')->maxLength(30),
                            TextInput::make('rib')->label('RIB Bancaire')->maxLength(30),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('email')->label('Email')->email()->nullable(),
                            TextInput::make('phone')->label('Téléphone')->nullable(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('city')->label('Ville')->nullable(),
                            TextInput::make('address')->label('Adresse')->nullable(),
                        ]),

                        Grid::make(2)->schema([
                            DatePicker::make('birth_date')->label('Date de naissance')->nullable(),
                            DatePicker::make('hire_date')->label("Date d'embauche")->nullable(),
                        ]),
                    ]),

                Tab::make('Contrat & Affectation')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('contract_type')
                                ->label('Type de contrat')
                                ->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC'])
                                ->required(),
                            Select::make('status')
                                ->label('Statut')
                                ->options(['actif' => 'Actif', 'inactif' => 'Inactif', 'suspendu' => 'Suspendu'])
                                ->required(),
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

                Tab::make('Situation familiale')
                    ->icon('heroicon-o-heart')
                    ->schema([
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

                Tab::make('Photo de profil')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        FileUpload::make('photo')
                            ->label('Photo de profil')
                            ->image()
                            ->disk('public')
                            ->directory('employees/photos')
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(2048)
                            ->avatar(),
                    ]),

            ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (Employee $record) =>
                        'https://ui-avatars.com/api/?name=' . urlencode($record->first_name . '+' . $record->last_name) . '&color=ffffff&background=0da8b1&bold=true'
                    )
                    ->size(38),

                TextColumn::make('full_name')
                    ->label('Employé')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable('last_name')
                    ->weight('semibold')
                    ->description(fn (Employee $record) => $record->matricule ?? '—'),

                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('department.name')
                    ->label('Département')
                    ->sortable()
                    ->default('—')
                    ->icon('heroicon-m-building-office'),

                TextColumn::make('position.title')
                    ->label('Poste')
                    ->sortable()
                    ->default('—'),

                TextColumn::make('contract_type')
                    ->label('Contrat')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'CDI'    => 'success',
                        'CDD'    => 'warning',
                        'Stage'  => 'info',
                        'ANAPEC' => 'gray',
                        default  => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'actif'    => 'success',
                        'inactif'  => 'danger',
                        'suspendu' => 'warning',
                        default    => 'gray',
                    }),

                TextColumn::make('hire_date')
                    ->label('Embauché le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Entreprise')->options(Company::pluck('name', 'id')),
                SelectFilter::make('status')->label('Statut')->options(['actif' => 'Actif', 'inactif' => 'Inactif', 'suspendu' => 'Suspendu']),
                SelectFilter::make('contract_type')->label('Type de contrat')->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC']),
                SelectFilter::make('department_id')->label('Département')->relationship('department', 'name'),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()->label('Supprimer la sélection'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('Aucun employé trouvé')
            ->emptyStateDescription('Commencez par ajouter votre premier employé.')
            ->emptyStateActions([
                \Filament\Actions\Action::make('create')
                    ->label('Ajouter un employé')
                    ->url(static::getUrl('create'))
                    ->icon('heroicon-o-user-plus'),
            ])
            ->defaultSort('last_name')
            ->striped();
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
