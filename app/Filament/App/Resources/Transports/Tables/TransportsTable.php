<?php

namespace App\Filament\App\Resources\Transports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class TransportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('matricule')->label('Matricule')->default('—'),
                \Filament\Tables\Columns\TextColumn::make('type')->label('Type')->badge()
                    ->color(fn ($state) => match($state) {
                        'bus' => 'info', 'minibus' => 'warning', 'voiture' => 'success', default => 'gray',
                    }),
                \Filament\Tables\Columns\TextColumn::make('chauffeurs_count')->label('Chauffeurs')->counts('chauffeurs')->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
