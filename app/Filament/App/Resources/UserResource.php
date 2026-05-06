<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Pages;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $slug = 'team';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Utilisateurs';
    protected static ?string $modelLabel = 'Utilisateur';
    protected static \UnitEnum|string|null $navigationGroup = 'Filament Shield';
    protected static ?int $navigationSort = 2;

    // Limiter la liste aux utilisateurs de la même company (sauf super-admin)
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()?->hasRole('super-admin')) {
            $query->where('company_id', auth()->user()?->company_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        $isSuperAdmin = auth()->user()?->hasRole('super-admin');

        // super-admin voit tous les rôles, admin voit tout sauf super-admin
        $excludes = $isSuperAdmin ? [] : ['super-admin'];

        $labels = [
            'super-admin' => 'Super Admin',
            'admin'       => 'Admin (company)',
            'rh'          => 'Responsable RH',
            'manager'     => 'Manager',
            'comptable'   => 'Comptable',
        ];

        $roles = Role::whereNotIn('name', $excludes)
            ->pluck('name', 'name')
            ->mapWithKeys(fn ($name) => [$name => $labels[$name] ?? $name]);

        return $schema->components([
            Section::make('Informations de connexion')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Nom complet')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Adresse email')
                        ->email()
                        ->required()
                        ->unique(table: 'users', column: 'email', ignoreRecord: true),
                ]),

                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->helperText('Laisser vide pour conserver le mot de passe actuel'),
            ]),

            Section::make('Rôle & Employé associé')->schema([
                Select::make('company_id')
                    ->label('Company')
                    ->options(\App\Models\Company::pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->visible($isSuperAdmin)
                    ->live()
                    ->helperText('Laisser vide = super-admin sans company'),

                Select::make('roles')
                    ->label('Rôle')
                    ->options($roles)
                    ->multiple()
                    ->preload()
                    ->required()
                    ->helperText(implode(' · ', [
                        'Super Admin : plateforme complète',
                        'Admin : company complète',
                        'RH : employés & congés',
                        'Manager : validation congés',
                        'Comptable : paie & déclarations',
                    ])),

                Select::make('employee_id')
                    ->label('Employé associé')
                    ->options(function () {
                        $query = Employee::withoutGlobalScopes();
                        if (! auth()->user()?->hasRole('super-admin')) {
                            $query->where('company_id', auth()->user()?->company_id);
                        }
                        return $query->get()->mapWithKeys(fn ($e) => [
                            $e->id => $e->full_name . ' — ' . $e->matricule,
                        ]);
                    })
                    ->searchable()
                    ->nullable()
                    ->helperText('Lie cet utilisateur à son dossier employé (accès "Mon espace")'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Rôle(s)')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'rh'        => 'primary',
                        'manager'   => 'warning',
                        'comptable' => 'success',
                        'admin'     => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'rh'        => 'Responsable RH',
                        'manager'   => 'Manager',
                        'comptable' => 'Comptable',
                        'admin'     => 'Admin',
                        default     => $state,
                    }),

                TextColumn::make('employee.full_name')
                    ->label('Employé associé')
                    ->default('—')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
