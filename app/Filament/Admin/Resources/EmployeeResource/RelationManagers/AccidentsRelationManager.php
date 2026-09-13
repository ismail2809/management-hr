<?php

namespace App\Filament\Admin\Resources\EmployeeResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccidentsRelationManager extends RelationManager
{
    protected static string $relationship = 'accidents';
    protected static ?string $title = 'Accidents de travail';

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            DateTimePicker::make('date_accident')
                ->label("Date & heure de l'accident")
                ->nullable()
                ->native(false)
                ->columnSpanFull(),

            Toggle::make('rembourse')
                ->label('Remboursé ?')
                ->inline(false)
                ->live(),

            TextInput::make('montant')
                ->label('Montant remboursé (MAD)')
                ->numeric()
                ->minValue(0)
                ->nullable()
                ->visible(fn (Get $get) => (bool) $get('rembourse')),

            Textarea::make('description')
                ->label('Description / détails')
                ->rows(3)
                ->nullable()
                ->columnSpanFull(),

            FileUpload::make('dossiers')
                ->label('Dossiers (PJ)')
                ->disk('public')
                ->directory(fn ($livewire) => 'employees/' . $livewire->getOwnerRecord()->id . '/accidents')
                ->multiple()
                ->reorderable()
                ->appendFiles()
                ->acceptedFileTypes([
                    'application/pdf', 'image/jpeg', 'image/png', 'image/webp',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])
                ->maxSize(10240)
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date_accident')
                    ->label("Date de l'accident")
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->default('—'),
                IconColumn::make('rembourse')
                    ->label('Remboursé')
                    ->boolean(),
                TextColumn::make('montant')
                    ->label('Montant (MAD)')
                    ->numeric(2)
                    ->default('—'),
                TextColumn::make('description')
                    ->label('Description')
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
                    ->label('Ajouter un accident')
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
