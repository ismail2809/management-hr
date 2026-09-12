<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasCompanyField;
use App\Filament\Admin\Resources\NiveauScolaireResource\Pages;
use App\Models\NiveauScolaire;
use Filament\Forms\Components\TextInput;
use App\Filament\Admin\Concerns\HasRoleBasedDelete;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NiveauScolaireResource extends Resource
{
    use HasRoleBasedDelete;

    use HasCompanyField;
    protected static ?string $model = NiveauScolaire::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Niveaux scolaires';
    protected static ?string $modelLabel = 'Niveau scolaire';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 21;

    public static function canViewAny(): bool { return auth()->user()?->hasRole('super-admin'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Niveau scolaire')->schema([
                static::companyField(),
                Grid::make(2)->schema([
                    TextInput::make('name')->label('Nom')->required()->maxLength(100),
                    TextInput::make('order')->label('Ordre d\'affichage')->numeric()->default(0),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')->label('#')->sortable(),
                TextColumn::make('name')->label('Niveau')->searchable()->sortable(),
                TextColumn::make('groupes_count')->label('Groupes')->counts('groupes')->sortable(),
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
            ->defaultSort('order')
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNiveauxScolaires::route('/'),
            'create' => Pages\CreateNiveauScolaire::route('/create'),
            'edit'   => Pages\EditNiveauScolaire::route('/{record}/edit'),
        ];
    }
}
