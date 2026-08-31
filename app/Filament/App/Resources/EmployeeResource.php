<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\EmployeeResource\Pages;
use App\Filament\App\Resources\EmployeeResource\RelationManagers\DocumentsRelationManager;
use App\Filament\App\Resources\EmployeeResource\RelationManagers\GroupesRelationManager;
use App\Models\Employee;
use App\Models\Groupe;
use App\Models\Profession;
use App\Models\Transport;
use Filament\Actions\ViewAction;
use Filament\Navigation\NavigationItem;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
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
    protected static \UnitEnum|string|null $navigationGroup = 'Personnel';
    protected static ?int $navigationSort = 1;

    /** Disabled for employee if the field already has a value in DB. */
    private static function d(string $field): \Closure
    {
        return fn ($record) => auth()->user()?->hasRole('employee') && filled($record?->{$field});
    }

    /** Always disabled for employees (admin-only fields). */
    private static function dAdmin(): \Closure
    {
        return fn () => (bool) auth()->user()?->hasRole('employee');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            /* ── Entreprise + Situation familiale (même ligne) ── */
            Grid::make(2)->schema([
                static::companyField(),

                /* ══ 4. Situation familiale ════════════════ */
                Section::make('Situation familiale')
                    ->icon('heroicon-o-heart')
                    ->visible(fn () => ! auth()->user()?->hasRole('employee'))
                    ->compact()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('marital_status')
                                ->label('État matrimonial')
                                ->options([
                                    'celibataire' => 'Célibataire',
                                    'marie'       => 'Marié(e)',
                                    'divorce'     => 'Divorcé(e)',
                                    'veuf'        => 'Veuf/Veuve',
                                ])
                                ->nullable()
                                ->live()
                                ->disabled(static::d('marital_status')),
                            TextInput::make('number_of_children')
                                ->label("Nombre d'enfants")
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->disabled(static::d('number_of_children')),
                        ]),
                    ]),
            ]),

            /* ══ 1. Identité ══════════════════════════════ */
            Section::make('Identité')
                ->icon('heroicon-o-identification')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('matricule')->label('Matricule')->maxLength(50)->disabled(static::dAdmin()),
                        TextInput::make('first_name')->label('Prénom')->required()->maxLength(100)->disabled(static::d('first_name')),
                        TextInput::make('last_name')->label('Nom')->required()->maxLength(100)->disabled(static::d('last_name')),
                    ]),
                    Grid::make(3)->schema([
                        Select::make('gender')
                            ->label('Sexe')
                            ->options(['M' => 'Masculin', 'F' => 'Féminin'])
                            ->nullable()
                            ->disabled(static::d('gender')),
                        TextInput::make('cin')->label('CIN')->maxLength(20)->disabled(static::d('cin')),
                        TextInput::make('cnss_number')->label('N° CNSS')->maxLength(30)->disabled(static::d('cnss_number')),
                    ]),
                    TextInput::make('rib')->label('RIB')->maxLength(30)->disabled(static::d('rib'))->columnSpanFull(),
                ]),

            /* ══ 2. Coordonnées ═══════════════════════════ */
            Section::make('Coordonnées')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('email')->label('Email')->email()->nullable()->disabled(static::d('email')),
                        TextInput::make('phone')->label('Téléphone mobile')->nullable()->disabled(static::d('phone')),
                        TextInput::make('phone_fixed')->label('Téléphone fixe')->nullable()->disabled(static::d('phone_fixed')),
                    ]),
                    Grid::make(3)->schema([
                        TextInput::make('city')->label('Ville')->nullable()->disabled(static::d('city')),
                        TextInput::make('address')->label('Adresse')->nullable()->disabled(static::d('address'))->columnSpan(2),
                    ]),
                ]),

            /* ══ 3. Informations personnelles ════════════ */
            Section::make('Informations personnelles')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Grid::make(3)->schema([
                        DatePicker::make('birth_date')->label('Date de naissance')->nullable()->disabled(static::d('birth_date')),
                        TextInput::make('birth_place')->label('Lieu de naissance')->nullable()->disabled(static::d('birth_place')),
                        TextInput::make('nationality')->label('Nationalité')->nullable()->disabled(static::d('nationality')),
                    ]),
                    Grid::make(3)->schema([
                        TextInput::make('diploma')->label('Diplôme')->nullable()->disabled(static::d('diploma')),
                        TextInput::make('promotion')->label('Promotion')->nullable()->disabled(static::d('promotion')),
                    ]),
                ]),

            /* ══ 5. Poste & Contrat ═══════════════════════ */
            Section::make('Poste & Contrat')
                ->icon('heroicon-o-briefcase')
                ->schema([
                    Grid::make(4)->schema([
                        Select::make('profession_id')
                            ->label('Profession')
                            ->relationship('profession', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->disabled(static::dAdmin())
                            ->columnSpan(2),
                        Select::make('profession_type')
                            ->label('Type de profession')
                            ->options([
                                'permanent'  => 'Permanent',
                                'stagiaire'  => 'Stagiaire',
                                'vacataire'  => 'Vacataire',
                            ])
                            ->nullable()
                            ->disabled(static::dAdmin()),
                        Select::make('contract_type')
                            ->label('Type de contrat')
                            ->options(['CDI' => 'CDI', 'CDD' => 'CDD', 'Stage' => 'Stage', 'ANAPEC' => 'ANAPEC'])
                            ->nullable()
                            ->disabled(static::dAdmin()),
                    ]),
                    Grid::make(4)->schema([
                        DatePicker::make('hire_date')->label("Date d'embauche")->nullable()->disabled(static::dAdmin())->columnSpan(2),
                        Select::make('status')
                            ->label('Statut')
                            ->options(['actif' => 'Actif', 'inactif' => 'Inactif', 'sorti' => 'Sorti'])
                            ->required()
                            ->live()
                            ->disabled(static::dAdmin()),
                    ]),
                ]),

            /* ══ 6. Affectations (conditionnel) ══════════ */
            Section::make('Affectations')
                ->icon('heroicon-o-squares-plus')
                ->visible(fn ($get) => in_array(
                    Profession::find($get('profession_id'))?->name,
                    ['Professeur', 'Chauffeur']
                ))
                ->schema([
                    Select::make('groupes')
                        ->label('Groupes affectés')
                        ->multiple()
                        ->relationship('groupes', 'name', fn ($query) => $query->join('niveaux_scolaires', 'niveaux_scolaires.id', '=', 'groupes.niveau_scolaire_id')->select('groupes.*', 'niveaux_scolaires.order as niveau_order')->orderBy('niveau_order'))
                        ->getOptionLabelFromRecordUsing(fn (Groupe $record) => "{$record->niveauScolaire?->name} — {$record->name}")
                        ->preload()
                        ->visible(fn ($get) => Profession::find($get('profession_id'))?->name === 'Professeur')
                        ->disabled(static::dAdmin()),
                    Select::make('transport_id')
                        ->label('Transport affecté')
                        ->relationship('transport', 'name')
                        ->getOptionLabelFromRecordUsing(fn (Transport $record) => "{$record->name}" . ($record->matricule ? " ({$record->matricule})" : ''))
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->visible(fn ($get) => Profession::find($get('profession_id'))?->name === 'Chauffeur')
                        ->disabled(static::dAdmin()),
                ]),

            /* ══ 7. Sortie (visible si statut = sorti) ═══ */
            Section::make('Informations de sortie')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->collapsed()
                ->visible(fn ($get) => $get('status') === 'sorti')
                ->schema([
                    Grid::make(3)->schema([
                        DatePicker::make('exit_date')->label('Date de sortie')->nullable()->disabled(static::dAdmin()),
                        Select::make('exit_reason')
                            ->label('Motif de sortie')
                            ->options([
                                'demission' => 'Démission',
                                'decision'  => 'Décision',
                                'sanction'  => 'Sanction',
                                'autre'     => 'Autre',
                            ])
                            ->nullable()
                            ->disabled(static::dAdmin()),
                    ]),
                    Textarea::make('exit_comment')->label('Commentaire')->rows(2)->nullable()->disabled(static::dAdmin()),
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

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if ($user?->hasRole('employee')) {
            return $record->id === $user->employee_id;
        }
        return true;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        if ($user?->hasRole('employee')) {
            return $record->id === $user->employee_id;
        }
        return true;
    }

    public static function canCreate(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'directeur']);
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'directeur']);
    }

    public static function canForceDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canForceDeleteAny(): bool
    {
        return false;
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
