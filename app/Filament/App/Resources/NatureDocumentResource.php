<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Concerns\HasRoleBasedDelete;
use App\Filament\App\Resources\NatureDocumentResource\Pages;
use App\Models\NatureDocument;
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

class NatureDocumentResource extends Resource
{
    use HasCompanyField, HasRoleBasedDelete;

    protected static ?string $model = NatureDocument::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'Natures de document';
    protected static ?string $modelLabel = 'Nature de document';
    protected static ?string $pluralModelLabel = 'Natures de document';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 27;

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
            Section::make('Nature de document')->schema([
                static::companyField(),

                TextInput::make('name')
                    ->label('Nom')
                    ->placeholder('Examen, Série d\'exercices, Contrôle continu…')
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
            'index'  => Pages\ListNatureDocuments::route('/'),
            'create' => Pages\CreateNatureDocument::route('/create'),
            'edit'   => Pages\EditNatureDocument::route('/{record}/edit'),
        ];
    }
}
