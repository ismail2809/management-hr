<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Filament\App\Resources\EmployeeResource\RelationManagers\DocumentsRelationManager;
use App\Filament\App\Resources\EmployeeResource\RelationManagers\GroupesRelationManager;
use App\Models\Employee;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
    use HasCompanyField;
    protected static ?string $model = Employee::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Employés';
    protected static ?string $modelLabel = 'Employé';
    protected static \UnitEnum|string|null $navigationGroup = 'Employés';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            static::companyField(),

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
                                TextInput::make('phone')->label('Téléphone mobile')->nullable(),
                            ]),
                            Grid::make(2)->schema([
                                TextInput::make('phone_fixed')->label('Téléphone fixe')->nullable(),
                                TextInput::make('city')->label('Ville')->nullable(),
                            ]),
                            TextInput::make('address')->label('Adresse')->nullable(),
                            Grid::make(2)->schema([
                                DatePicker::make('birth_date')->label('Date de naissance')->nullable(),
                                TextInput::make('birth_place')->label('Lieu de naissance')->nullable(),
                            ]),
                            Grid::make(2)->schema([
                                TextInput::make('diploma')->label('Diplôme')->nullable(),
                                TextInput::make('promotion')->label('Promotion')->nullable(),
                            ]),
                        ]),

                    Section::make('Sortie')
                        ->icon('heroicon-o-arrow-right-on-rectangle')
                        ->collapsed()
                        ->visible(fn (Get $get) => $get('status') === 'sorti')
                        ->schema([
                            Grid::make(2)->schema([
                                DatePicker::make('exit_date')->label('Date de sortie')->nullable(),
                                Select::make('exit_reason')
                                    ->label('Motif de sortie')
                                    ->options([
                                        'demission' => 'Démission',
                                        'decision'  => 'Décision',
                                        'sanction'  => 'Sanction',
                                        'autre'     => 'Autre',
                                    ])
                                    ->nullable(),
                            ]),
                            Textarea::make('exit_comment')->label('Commentaire')->rows(2)->nullable(),
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
                                ->label('Situation matrimoniale')
                                ->options([
                                    'celibataire' => 'Célibataire',
                                    'marie'       => 'Marié(e)',
                                    'divorce'     => 'Divorcé(e)',
                                    'veuf'        => 'Veuf/Veuve',
                                ])
                                ->required()
                                ->live(),
                            TextInput::make('number_of_children')
                                ->label("Nombre d'enfants")
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->visible(fn (Get $get) => $get('marital_status') === 'marie'),
                        ]),

                ])->columnSpan(1),

            ]),

            Section::make('Poste & Contrat')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Grid::make(3)->schema([
                        Select::make('profession_id')
                            ->label('Profession')
                            ->relationship('profession', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live(),
                        Select::make('profession_type')
                            ->label('Type de profession')
                            ->options([
                                'permanent'  => 'Permanent',
                                'stagiaire'  => 'Stagiaire',
                                'vacataire'  => 'Vacataire',
                            ])
                            ->nullable(),
                        Select::make('contract_type')
                            ->label('Type de contrat')
                            ->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC'])
                            ->required(),
                    ]),
                    Grid::make(3)->schema([
                        DatePicker::make('hire_date')->label("Date d'embauche")->nullable(),
                        Select::make('status')
                            ->label('Statut')
                            ->options(['actif' => 'Actif', 'inactif' => 'Inactif', 'sorti' => 'Sorti'])
                            ->required()
                            ->live(),
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
                TextColumn::make('profession.name')->label('Profession')->badge()->color('primary')->default('—')->sortable(),
                TextColumn::make('profession_type')->label('Type')->badge()
                    ->color(fn ($state) => match ($state) {
                        'permanent'  => 'success',
                        'stagiaire'  => 'info',
                        'vacataire'  => 'warning',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'permanent'  => 'Permanent',
                        'stagiaire'  => 'Stagiaire',
                        'vacataire'  => 'Vacataire',
                        default      => '—',
                    }),
                TextColumn::make('contract_type')->label('Contrat')->badge()
                    ->color(fn ($state) => match ($state) {
                        'CDI' => 'success', 'CDD' => 'warning', 'Stage' => 'info', 'ANAPEC' => 'gray', default => 'gray',
                    }),
                TextColumn::make('status')->label('Statut')->badge()
                    ->color(fn ($state) => match ($state) {
                        'actif'   => 'success',
                        'inactif' => 'danger',
                        'sorti'   => 'gray',
                        default   => 'gray',
                    }),
                TextColumn::make('hire_date')->label('Embauche')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(['actif' => 'Actif', 'inactif' => 'Inactif', 'sorti' => 'Sorti']),
                SelectFilter::make('profession_id')->label('Profession')->relationship('profession', 'name'),
                SelectFilter::make('profession_type')->label('Type')->options([
                    'permanent' => 'Permanent', 'stagiaire' => 'Stagiaire', 'vacataire' => 'Vacataire',
                ]),
                SelectFilter::make('contract_type')->label('Contrat')->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC']),
            ])
            ->defaultSort('last_name')
            ->recordUrl(fn (Employee $record) => static::getUrl('view', ['record' => $record]))
            ->actions([ViewAction::make()->label('Profil')])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function canViewAny(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            GroupesRelationManager::class,
        ];
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
