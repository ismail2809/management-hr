<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Employés';
    protected static ?string $modelLabel = 'Employé';
    protected static \UnitEnum|string|null $navigationGroup = 'Employés';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Grid::make(3)->schema([

                /* ── Colonne gauche (2/3) ── */
                Group::make([

                    Section::make('Identité')
                        ->icon('heroicon-o-identification')
                        ->schema([
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
                                TextInput::make('city')->label('Ville')->nullable(),
                                TextInput::make('address')->label('Adresse')->nullable(),
                            ]),
                            Grid::make(2)->schema([
                                DatePicker::make('birth_date')->label('Date de naissance')->nullable(),
                                DatePicker::make('hire_date')->label("Date d'embauche")->nullable(),
                            ]),
                        ]),

                ])->columnSpan(2),

                /* ── Colonne droite (1/3) ── */
                Group::make([

                    Section::make('Photo')
                        ->icon('heroicon-o-camera')
                        ->schema([
                            FileUpload::make('photo')
                                ->label('Photo de profil')
                                ->image()
                                ->disk('public')
                                ->directory('employees/photos')
                                ->imageEditor()
                                ->circleCropper()
                                ->maxSize(2048),
                        ]),

                    Section::make('Situation familiale')
                        ->icon('heroicon-o-heart')
                        ->schema([
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

                ])->columnSpan(1),

            ]),

            /* ── Pleine largeur ── */
            Section::make('Contrat & Affectation')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Grid::make(4)->schema([
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
                            ->preload()
                            ->nullable(),
                        Select::make('position_id')
                            ->label('Poste')
                            ->relationship('position', 'title')
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\Position $record)
                                => implode(' — ', array_filter([
                                    $record->company?->name,
                                    $record->title,
                                    $record->category,
                                ])))
                            ->searchable()
                            ->preload()
                            ->nullable(),
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
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=E&color=0da8b1&background=e0f7fa')
                    ->size(40),
                TextColumn::make('matricule')->label('Matricule')->searchable()->sortable(),
                TextColumn::make('full_name')->label('Nom complet')->searchable(['first_name', 'last_name'])->sortable('last_name'),
                TextColumn::make('department.name')->label('Département')->sortable()->default('—'),
                TextColumn::make('position.title')->label('Poste')->sortable()->default('—'),
                TextColumn::make('contract_type')->label('Contrat')->badge()
                    ->color(fn ($state) => match ($state) {
                        'CDI' => 'success', 'CDD' => 'warning', 'Stage' => 'info', 'ANAPEC' => 'gray', default => 'gray',
                    }),
                TextColumn::make('status')->label('Statut')->badge()
                    ->color(fn ($state) => match ($state) {
                        'actif' => 'success', 'inactif' => 'danger', 'suspendu' => 'warning', default => 'gray',
                    }),
                TextColumn::make('hire_date')->label('Embauche')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(['actif' => 'Actif', 'inactif' => 'Inactif', 'suspendu' => 'Suspendu']),
                SelectFilter::make('contract_type')->label('Type contrat')->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC']),
                SelectFilter::make('department_id')->label('Département')->relationship('department', 'name'),
            ])
            ->defaultSort('last_name')
            ->recordUrl(fn (Employee $record) => static::getUrl('view', ['record' => $record]))
            ->actions([
                ViewAction::make()->label('Profil'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewAny(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view'   => Pages\ViewEmployee::route('/{record}'),
            'edit'   => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
