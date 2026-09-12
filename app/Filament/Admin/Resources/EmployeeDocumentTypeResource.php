<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasCompanyField;
use App\Filament\Admin\Concerns\HasRoleBasedDelete;
use App\Filament\Admin\Resources\EmployeeDocumentTypeResource\Pages;
use App\Models\EmployeeDocumentType;
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

class EmployeeDocumentTypeResource extends Resource
{
    use HasCompanyField, HasRoleBasedDelete;

    protected static ?string $model = EmployeeDocumentType::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Types de document employé';
    protected static ?string $modelLabel = 'Type de document employé';
    protected static ?string $pluralModelLabel = 'Types de document employé';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 29;

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
            Section::make('Type de document employé')->schema([
                static::companyField(),

                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Nom')
                        ->placeholder('CIN, Diplôme, Contrat…')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('code')
                        ->label('Code')
                        ->placeholder('cin, diplome, contrat_anapec…')
                        ->maxLength(100),
                ]),

                Grid::make(2)->schema([
                    TextInput::make('sort_order')
                        ->label("Ordre d'affichage")
                        ->numeric()
                        ->default(0),

                    Toggle::make('active')
                        ->label('Actif')
                        ->default(true),
                ]),
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

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),

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
            'index'  => Pages\ListEmployeeDocumentTypes::route('/'),
            'create' => Pages\CreateEmployeeDocumentType::route('/create'),
            'edit'   => Pages\EditEmployeeDocumentType::route('/{record}/edit'),
        ];
    }
}
