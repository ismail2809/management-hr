<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DepartmentResource\Pages;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
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

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Départements';
    protected static ?string $modelLabel = 'Département';
    protected static \UnitEnum|string|null $navigationGroup = 'Organisation';
    protected static ?int $navigationSort = 7;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Entreprise & Département')->schema([
                Grid::make(2)->schema([
                    Select::make('company_id')
                        ->label('Entreprise')
                        ->options(Company::pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('manager_id', null)),

                    TextInput::make('name')
                        ->label('Nom du département')
                        ->required()
                        ->maxLength(255),
                ]),

                Select::make('manager_id')
                    ->label('Responsable')
                    ->options(fn ($get) => Employee::withoutGlobalScopes()
                        ->when($get('company_id'), fn ($q, $cid) => $q->where('company_id', $cid))
                        ->get()->pluck('full_name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->helperText('Optionnel — peut être défini plus tard'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Entreprise')->sortable()->badge()->color('gray'),
                TextColumn::make('name')->label('Département')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('manager.full_name')->label('Responsable')->default('—')->sortable(),
                TextColumn::make('employees_count')->label('Effectif')->counts('employees')->sortable()->badge()->color('primary'),
                TextColumn::make('created_at')->label('Créé le')->date('d/m/Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Entreprise')->options(Company::pluck('name', 'id')),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit'   => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
