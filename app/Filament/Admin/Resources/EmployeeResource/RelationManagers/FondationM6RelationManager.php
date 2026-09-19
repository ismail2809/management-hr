<?php

namespace App\Filament\Admin\Resources\EmployeeResource\RelationManagers;

use App\Rules\SafeFileUpload;
use Filament\Forms\Components\FileUpload;
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

class FondationM6RelationManager extends RelationManager
{
    protected static string $relationship = 'fondationM6';
    protected static ?string $title = 'Fondation M6';

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('annee_scolaire')
                ->label('Année scolaire')
                ->placeholder('2025-2026')
                ->nullable(),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),

            FileUpload::make('fichiers')
                ->label('Fichiers justificatifs')
                ->disk('public')
                ->directory(fn ($livewire) => 'employees/' . $livewire->getOwnerRecord()->id . '/fondation-m6')
                ->multiple()
                ->reorderable()
                ->appendFiles()
                ->acceptedFileTypes([
                    'application/pdf', 'image/jpeg', 'image/png', 'image/webp',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])
                ->maxSize(10240)
                ->rules([new SafeFileUpload()])
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('annee_scolaire')
                    ->label('Année scolaire')
                    ->default('—'),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(60)
                    ->default('—'),
                TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Ajouter')
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
