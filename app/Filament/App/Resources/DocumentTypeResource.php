<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\DocumentTypeResource\Pages;
use App\Models\DocumentType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentTypeResource extends Resource
{
    use HasCompanyField;

    protected static ?string $model = DocumentType::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Type de demande';
    protected static ?string $pluralModelLabel = 'Types de demande';
    protected static \UnitEnum|string|null $navigationGroup = 'Paramétrage';
    protected static ?int $navigationSort = 7;

    public static function canViewAny(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Type de demande')->schema([
                static::companyField(),

                Grid::make(2)->schema([
                    TextInput::make('name')
                        ->label('Nom')
                        ->placeholder('Ex : Activités, Matériel, Attestation de travail…')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('code')
                        ->label('Code')
                        ->placeholder('Ex : activites, materiel, attestation_travail…')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                ]),

                Grid::make(2)->schema([
                    Select::make('categorie')
                        ->label('Catégorie')
                        ->options([
                            'document' => 'Document administratif',
                            'autre'    => 'Autre demande',
                        ])
                        ->required(),

                    TextInput::make('sort_order')
                        ->label('Ordre d\'affichage')
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

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('categorie')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn ($state) => $state === 'document' ? 'primary' : 'warning')
                    ->formatStateUsing(fn ($state) => $state === 'document' ? 'Document administratif' : 'Autre demande'),

                TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable(),

                IconColumn::make('active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('categorie')
                    ->label('Catégorie')
                    ->options(['document' => 'Document administratif', 'autre' => 'Autre demande']),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'edit'   => Pages\EditDocumentType::route('/{record}/edit'),
        ];
    }
}
