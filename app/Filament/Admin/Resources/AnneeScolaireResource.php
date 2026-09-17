<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasCompanyField;
use App\Filament\Admin\Concerns\HasRoleBasedDelete;
use App\Filament\Admin\Resources\AnneeScolaireResource\Pages;
use App\Models\AnneeScolaire;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnneeScolaireResource extends Resource
{
    use HasRoleBasedDelete, HasCompanyField;

    protected static ?string $model = AnneeScolaire::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Années scolaires';
    protected static ?string $modelLabel = 'Année scolaire';
    protected static ?string $pluralModelLabel = 'Années scolaires';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 21;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('super-admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Année scolaire')->schema([
                static::companyField(),

                TextInput::make('name')
                    ->label('Année scolaire')
                    ->placeholder('2025-2026')
                    ->required()
                    ->maxLength(9)
                    ->regex('/^\d{4}-\d{4}$/')
                    ->validationMessages(['regex' => 'Format attendu : AAAA-AAAA (ex: 2025-2026)'])
                    ->helperText('Format : AAAA-AAAA — ex: 2025-2026'),

                Toggle::make('is_active')
                    ->label('Année en cours')
                    ->helperText('Marque cette année comme année scolaire active (pré-sélectionnée dans les formulaires).')
                    ->default(false),

                Textarea::make('note')
                    ->label('Note')
                    ->rows(2)
                    ->nullable()
                    ->maxLength(500),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Année scolaire')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                IconColumn::make('is_active')
                    ->label('En cours')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('note')
                    ->label('Note')
                    ->limit(60)
                    ->default('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'desc')
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnneesScolaires::route('/'),
            'create' => Pages\CreateAnneeScolaire::route('/create'),
            'edit'   => Pages\EditAnneeScolaire::route('/{record}/edit'),
        ];
    }
}
