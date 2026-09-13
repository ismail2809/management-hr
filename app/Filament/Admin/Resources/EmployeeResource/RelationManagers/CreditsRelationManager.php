<?php

namespace App\Filament\Admin\Resources\EmployeeResource\RelationManagers;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CreditsRelationManager extends RelationManager
{
    protected static string $relationship = 'credits';
    protected static ?string $title = 'Crédits';

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            CheckboxList::make('types')
                ->label('Type(s) de crédit')
                ->options([
                    'voiture'      => 'Voiture',
                    'immobilier'   => 'Immobilier',
                    'consommation' => 'Consommation',
                    'education'    => 'Éducation',
                    'autre'        => 'Autre',
                ])
                ->columns(3)
                ->nullable()
                ->columnSpanFull(),

            TextInput::make('montant')
                ->label('Montant total (MAD)')
                ->numeric()
                ->minValue(0)
                ->nullable(),

            TextInput::make('mensualite')
                ->label('Mensualité (MAD)')
                ->numeric()
                ->minValue(0)
                ->nullable(),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('types')
                    ->label('Type(s)')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : ($state ?? '—'))
                    ->badge(),
                TextColumn::make('montant')
                    ->label('Montant (MAD)')
                    ->numeric(2)
                    ->default('—'),
                TextColumn::make('mensualite')
                    ->label('Mensualité (MAD)')
                    ->numeric(2)
                    ->default('—'),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->default('—'),
                TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Ajouter un crédit')
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, [
                        'ecole_setting_id' => $this->getOwnerRecord()->ecole_setting_id,
                    ])),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
