<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\EcoleSettings;
use App\Models\Employee;
use App\Models\User;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use App\Filament\Admin\Concerns\HasRoleBasedDelete;
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

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
    protected static ?string $modelLabel = 'Utilisateur';
    protected static \UnitEnum|string|null $navigationGroup = 'Filament Shield';
    protected static ?int $navigationSort = 2;

    // Limiter la liste aux utilisateurs de la même company (sauf super-admin)
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()?->hasRole('super-admin')) {
            $query->where('ecole_setting_id', auth()->user()?->ecole_setting_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        $isSuperAdmin = auth()->user()?->hasRole('super-admin');

        // Lire tous les rôles depuis Spatie (comme Shield)
        $rolesQuery = Role::orderBy('name');
        if (! $isSuperAdmin) {
            $rolesQuery->where('name', '!=', 'super-admin');
        }
        $roles = $rolesQuery->pluck('name', 'name');

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
                Hidden::make('ecole_setting_id')
                    ->default(auth()->user()?->ecole_setting_id ?? \App\Models\EcoleSettings::withoutGlobalScopes()->value('id'))
                    ->dehydrated(),

                Select::make('roles')
                    ->label('Rôle')
                    ->options($roles)
                    ->required(),

                Select::make('employee_id')
                    ->label('Employé associé')
                    ->options(function () {
                        $query = Employee::withoutGlobalScopes();
                        if (! auth()->user()?->hasRole('super-admin')) {
                            $query->where('ecole_setting_id', auth()->user()?->ecole_setting_id);
                        }
                        return $query->with('profession')->get()->mapWithKeys(fn ($e) => [
                            $e->id => $e->id . ' — ' . $e->full_name . ($e->profession ? ' — ' . $e->profession->name : ''),
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
                        'super-admin'  => 'danger',
                        'directeur'    => 'warning',
                        'secretaire'   => 'primary',
                        'surveillante' => 'info',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super-admin'          => 'Super Admin',
                        'directeur'            => 'Directeur',
                        'secretaire'           => 'Secrétaire',
                        'surveillante'         => 'Surveillant(e) général(e)',
                        'femme-de-menage'      => 'Femme de ménage',
                        'assistante-transport' => 'Assistante transport',
                        default                => $state,
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
        return ! auth()->user()?->isBasicRole();
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
