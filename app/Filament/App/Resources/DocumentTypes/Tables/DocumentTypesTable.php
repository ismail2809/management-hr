<?php

namespace App\Filament\App\Resources\DocumentTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentTypesTable
{
    public static function configure(Table $table): Table
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
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('categorie')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn ($state) => $state === 'document' ? 'primary' : 'warning')
                    ->formatStateUsing(fn ($state) => $state === 'document' ? 'Document administratif' : 'Autre demande'),

                TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('categorie')
                    ->label('Catégorie')
                    ->options(['document' => 'Document administratif', 'autre' => 'Autre demande']),

                SelectFilter::make('active')
                    ->label('Statut')
                    ->options([1 => 'Actif', 0 => 'Inactif']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }
}
