<?php

namespace App\Filament\App\Resources\EmployeeResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\BulkActionGroup;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'Documents';

    public static array $typeLabels = [
        'photo'           => 'Photo',
        'extrait_naissance' => 'Extrait de naissance',
        'cin'             => 'CIN (scan)',
        'carte_cnss'      => 'Carte CNSS',
        'rib'             => 'RIB (scan)',
        'diplome'         => 'Diplôme',
        'contrat_anapec'  => 'Contrat ANAPEC',
        'autre'           => 'Autre',
    ];

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type_document')
                ->label('Type de document')
                ->options(self::$typeLabels)
                ->required(),
            TextInput::make('name')
                ->label('Nom du document')
                ->required()
                ->maxLength(200),
            FileUpload::make('file_path')
                ->label('Fichier')
                ->disk('public')
                ->directory(fn ($livewire) => 'employees/' . $livewire->getOwnerRecord()->id . '/documents')
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->maxSize(5120)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type_document')
                    ->label('Type')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => self::$typeLabels[$state] ?? $state),
                TextColumn::make('name')->label('Nom')->searchable(),
                TextColumn::make('created_at')->label('Uploadé le')->date('d/m/Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([CreateAction::make()->label('Ajouter un document')])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
