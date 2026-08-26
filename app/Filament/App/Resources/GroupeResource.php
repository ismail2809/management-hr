<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\GroupeResource\Pages;
use App\Models\Groupe;
use App\Models\NiveauScolaire;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GroupeResource extends Resource
{
    use HasCompanyField;
    protected static ?string $model = Groupe::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'Groupes';
    protected static ?string $modelLabel = 'Groupe';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            static::companyField(),
            Select::make('niveau_scolaire_id')
                ->label('Niveau scolaire')
                ->relationship('niveauScolaire', 'name', fn ($query) => $query->orderBy('order'))
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('name')->label('Nom du groupe')->required()->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('niveauScolaire.name')->label('Niveau')->sortable()->badge()->color('info'),
                TextColumn::make('name')->label('Groupe')->searchable()->sortable(),
                TextColumn::make('employees_count')->label('Professeurs affectés')->counts('employees')->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->join('niveaux_scolaires', 'niveaux_scolaires.id', '=', 'groupes.niveau_scolaire_id')->select('groupes.*'))
            ->defaultSort('niveaux_scolaires.order')
            ->filters([
                SelectFilter::make('niveau_scolaire_id')
                    ->label('Niveau')
                    ->relationship('niveauScolaire', 'name'),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGroupes::route('/'),
            'create' => Pages\CreateGroupe::route('/create'),
            'edit'   => Pages\EditGroupe::route('/{record}/edit'),
        ];
    }
}
