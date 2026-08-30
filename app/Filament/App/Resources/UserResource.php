<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Pages;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use App\Filament\App\Concerns\HasRoleBasedDelete;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    use HasRoleBasedDelete;

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

        $allowed = $isSuperAdmin
            ? ['super-admin', 'directeur', 'secretaire', 'surveillante', 'employee']
            : ['directeur', 'secretaire', 'surveillante', 'employee'];

        $labels = [
            'super-admin'  => 'Super Admin',
            'directeur'    => 'Directeur',
            'secretaire'   => 'Secrétaire',
            'surveillante' => 'Surveillante',
            'employee'     => 'Employé',
        ];

        $roles = Role::whereIn('name', $allowed)
            ->pluck('name', 'name')
            ->mapWithKeys(fn ($name) => [$name => $labels[$name] ?? $name]);

        return $schema->columns(1)->components([
            Section::make('Informations de connexion')->columns(3)->schema([
                    TextInput::make('name')
                        ->label('Nom complet')
                        ->required()
                        ->maxLength(255)
                        ->disabled(fn (Get $get) => filled($get('employee_id')) && filled(Employee::withoutGlobalScopes()->find($get('employee_id'))?->full_name))
                        ->dehydrated(),

                    TextInput::make('email')
                        ->label('Adresse email')
                        ->email()
                        ->required()
                        ->unique(table: 'users', column: 'email', ignoreRecord: true)
                        ->disabled(fn (Get $get) => filled($get('employee_id')) && filled(Employee::withoutGlobalScopes()->find($get('employee_id'))?->email))
                        ->dehydrated(),

                TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->helperText('Laisser vide pour conserver le mot de passe actuel'),
            ]),

            Section::make('Rôle & Employé associé')->columns(3)->schema([
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
                    ->default('employee')
                    ->required()
                    ->helperText(implode(' · ', [
                        'Super Admin : plateforme complète',
                        'Secrétaire : gestion complète de la company',
                        'Employé : accès limité à son espace personnel',
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
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (! $state) {
                            $set('email', null);
                            $set('name', null);
                            return;
                        }
                        $employee = Employee::withoutGlobalScopes()->find($state);
                        if (! $employee) {
                            return;
                        }
                        $set('email', $employee->email ?? null);
                        $set('name', $employee->full_name);
                    })
                    ->helperText('Sélectionner un employé remplit automatiquement le nom et l\'email.'),
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
                        'super-admin' => 'danger',
                        'secretaire'  => 'primary',
                        'employee'    => 'gray',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super-admin' => 'Super Admin',
                        'secretaire'  => 'Secrétaire',
                        'employee'    => 'Employé',
                        default       => $state,
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
