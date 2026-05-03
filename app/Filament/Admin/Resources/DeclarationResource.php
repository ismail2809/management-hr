<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DeclarationResource\Pages;
use App\Models\Company;
use App\Models\Declaration;
use Filament\Actions\Action;
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

class DeclarationResource extends Resource
{
    protected static ?string $model = Declaration::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Déclarations';
    protected static ?string $modelLabel = 'Déclaration';
    protected static \UnitEnum|string|null $navigationGroup = 'Légal';
    protected static ?int $navigationSort = 11;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Entreprise & Type')->schema([
                Select::make('company_id')
                    ->label('Entreprise')
                    ->options(Company::pluck('name', 'id'))
                    ->required(),

                Grid::make(2)->schema([
                    Select::make('type')
                        ->label('Type de déclaration')
                        ->options(['CNSS' => 'CNSS', 'IR' => 'IR', 'Etat_9421' => 'État 9421'])
                        ->required(),

                    Select::make('status')
                        ->label('Statut')
                        ->options(['en_cours' => 'En cours', 'générée' => 'Générée', 'soumise' => 'Soumise'])
                        ->default('en_cours')
                        ->required(),
                ]),

                Grid::make(2)->schema([
                    Select::make('month')
                        ->label('Mois')
                        ->options([1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'])
                        ->required(),

                    TextInput::make('year')->label('Année')->numeric()->default(now()->year)->required(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Entreprise')->sortable()->badge()->color('gray'),
                TextColumn::make('type')->label('Type')->badge()
                    ->color(fn ($state) => match ($state) {
                        'CNSS' => 'info', 'IR' => 'warning', 'Etat_9421' => 'gray', default => 'gray',
                    }),
                TextColumn::make('periode_label')->label('Période')->weight('semibold'),
                TextColumn::make('status')->label('Statut')->badge()
                    ->color(fn ($state) => match ($state) {
                        'en_cours' => 'gray', 'générée' => 'warning', 'soumise' => 'success', default => 'gray',
                    }),
                TextColumn::make('generated_file_path')->label('Fichier')->default('—')->limit(40)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Créée le')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Entreprise')->options(Company::pluck('name', 'id')),
                SelectFilter::make('type')->label('Type')->options(['CNSS' => 'CNSS', 'IR' => 'IR', 'Etat_9421' => 'État 9421']),
                SelectFilter::make('status')->label('Statut')->options(['en_cours' => 'En cours', 'générée' => 'Générée', 'soumise' => 'Soumise']),
            ])
            ->actions([
                Action::make('mark_submitted')
                    ->label('Marquer soumise')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (Declaration $record) => $record->status === 'générée')
                    ->requiresConfirmation()
                    ->action(fn (Declaration $record) => $record->update(['status' => 'soumise'])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDeclarations::route('/'),
            'create' => Pages\CreateDeclaration::route('/create'),
            'edit'   => Pages\EditDeclaration::route('/{record}/edit'),
        ];
    }
}
