<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Concerns\HasRoleBasedDelete;
use App\Filament\App\Resources\CategorieAutreDemandeResource\Pages;
use App\Models\CategorieAutreDemande;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CategorieAutreDemandeResource extends Resource
{
    use HasCompanyField, HasRoleBasedDelete;

    protected static ?string $model = CategorieAutreDemande::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Catégories de demande';
    protected static ?string $modelLabel = 'Catégorie de demande';
    protected static ?string $pluralModelLabel = 'Catégories de demande';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 28;

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()?->hasRole('super-admin')) {
            return parent::getEloquentQuery()->withoutGlobalScopes();
        }

        return parent::getEloquentQuery();
    }

    public static function canViewAny(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Catégorie de demande')->schema([
                static::companyField(),

                TextInput::make('name')
                    ->label('Nom')
                    ->placeholder('Pédagogique, Administratif, Technique…')
                    ->required()
                    ->maxLength(100),

                TextInput::make('sort_order')
                    ->label("Ordre d'affichage")
                    ->numeric()
                    ->default(0),

                Toggle::make('active')
                    ->label('Actif')
                    ->default(true),
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

                TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),

                IconColumn::make('active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('active')->label('Actif'),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategorieAutreDemandes::route('/'),
            'create' => Pages\CreateCategorieAutreDemande::route('/create'),
            'edit'   => Pages\EditCategorieAutreDemande::route('/{record}/edit'),
        ];
    }
}
