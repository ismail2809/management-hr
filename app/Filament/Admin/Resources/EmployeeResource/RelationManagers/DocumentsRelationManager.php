<?php

namespace App\Filament\App\Resources\EmployeeResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $title = 'Documents';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

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
            FileUpload::make('file_path')
                ->label('Fichier')
                ->disk('public')
                ->directory(fn ($livewire) => 'employees/' . $livewire->getOwnerRecord()->id . '/documents')
                ->preserveFilenames()
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/*',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])
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
            ->headerActions([
                CreateAction::make()
                    ->label('Ajouter un document')
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, [
                        'company_id' => $this->getOwnerRecord()->company_id,
                        'name'       => basename($data['file_path']),
                    ])),
            ])
            ->actions([
                \Filament\Actions\Action::make('download')
                    ->label('Télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
