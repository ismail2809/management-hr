<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasCompanyField;
use App\Filament\Admin\Concerns\HasRoleBasedDelete;
use App\Filament\Admin\Resources\NatureDocumentResource\Pages;
use App\Models\NatureDocument;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
        return auth()->user()?->hasRole('super-admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Nature de document')->schema([
                static::companyField(),

                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Nom')
                        ->placeholder('Examen, Série d\'exercices, Contrôle continu…')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('sort_order')
                        ->label("Ordre d'affichage")
                        ->numeric()
                        ->default(0),
                ]),

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
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Supprimé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('active')->label('Actif'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
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
