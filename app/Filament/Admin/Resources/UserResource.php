<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Utilisateurs';
    protected static ?string $modelLabel = 'Utilisateur';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
            ]),

            TextInput::make('password')
                ->label('Mot de passe')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation) => $operation === 'create')
                ->helperText('Laisser vide pour ne pas modifier'),

            Grid::make(2)->schema([
                Select::make('company_id')
                    ->label('Company')
                    ->options(Company::pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('employee_id', null))
                    ->helperText('Vide = super-admin sans company'),

                Select::make('roles')
                    ->label('Rôle')
                    ->options(Role::pluck('name', 'name'))
                    ->multiple()
                    ->preload()
                    ->required(),
            ]),

            Select::make('employee_id')
                ->label('Lier à un employé')
                ->options(fn ($get) => Employee::withoutGlobalScopes()
                    ->when($get('company_id'), fn ($q, $cid) => $q->where('company_id', $cid))
                    ->get()
                    ->mapWithKeys(fn ($e) => [$e->id => $e->full_name . ' (' . $e->matricule . ')'])
                )
                ->searchable()
                ->nullable()
                ->helperText('Pré-remplira automatiquement l\'employé dans tous les formulaires'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('company.name')->label('Company')->default('—')->sortable(),
                TextColumn::make('employee.full_name')->label('Employé lié')->default('—'),
                TextColumn::make('roles.name')->label('Rôle(s)')->badge(),
                TextColumn::make('created_at')->label('Créé le')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->options(Company::pluck('name', 'id'))
                    ->placeholder('Toutes'),
            ])
            ->actions([
                Action::make('impersonate')
                    ->label('Se connecter en tant que')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->visible(fn (User $record) => !$record->hasRole('super-admin'))
                    ->url(fn (User $record) => '#') // placeholder — à connecter avec un package impersonate si besoin
                    ->openUrlInNewTab(),
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
