<?php

namespace App\Filament\App\Resources\EmployeeResource\RelationManagers;

use App\Models\Groupe;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\BulkActionGroup;

class GroupesRelationManager extends RelationManager
{
    protected static string $relationship = 'groupes';
    protected static ?string $title = 'Groupes affectés';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('groupe_id')
                ->label('Groupe')
                ->options(
                    Groupe::withoutGlobalScopes()
                        ->with('niveauScolaire')
                        ->get()
                        ->mapWithKeys(fn ($g) => [$g->id => "{$g->niveauScolaire?->name} — {$g->name}"])
                )
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('niveauScolaire.name')->label('Niveau')->badge()->color('info'),
                TextColumn::make('name')->label('Groupe'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn ($query) => $query->with('niveauScolaire'))
                    ->recordTitle(fn (Groupe $record) => "{$record->niveauScolaire?->name} — {$record->name}"),
            ])
            ->actions([DetachAction::make()])
            ->bulkActions([BulkActionGroup::make([DetachBulkAction::make()])]);
    }
}
