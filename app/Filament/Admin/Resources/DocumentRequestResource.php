<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DocumentRequestResource\Pages;
use App\Models\Company;
use App\Models\DocumentRequest;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentRequestResource extends Resource
{
    protected static ?string $model = DocumentRequest::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationLabel = 'Demandes de documents';
    protected static ?string $modelLabel = 'Demande de document';
    protected static \UnitEnum|string|null $navigationGroup = 'Légal';
    protected static ?int $navigationSort = 12;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Entreprise & Demandeur')->schema([
                Grid::make(2)->schema([
                    Select::make('company_id')
                        ->label('Entreprise')
                        ->options(Company::pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('employee_id', null)),

                    Select::make('employee_id')
                        ->label('Employé')
                        ->options(fn ($get) => Employee::withoutGlobalScopes()
                            ->when($get('company_id'), fn ($q, $cid) => $q->where('company_id', $cid))
                            ->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->required(),
                ]),

                Select::make('type')
                    ->label('Type de document')
                    ->options(DocumentRequest::$typeLabels)
                    ->required(),

                Radio::make('format')
                    ->label('Format souhaité')
                    ->options(['digital' => 'Version digitale (PDF)', 'papier' => 'Version papier'])
                    ->default('digital')
                    ->inline()
                    ->required(),
            ]),

            Section::make('Détails & Statut')->schema([
                Textarea::make('reason')->label('Remarques / raison de la demande')->rows(3)->nullable(),

                Select::make('status')
                    ->label('Statut')
                    ->options(['en_attente' => 'En attente', 'traité' => 'Traité', 'refusé' => 'Refusé'])
                    ->default('en_attente')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Entreprise')->sortable()->badge()->color('gray'),
                TextColumn::make('employee.full_name')->label('Employé')->searchable(['employees.first_name', 'employees.last_name'])->sortable()->weight('semibold'),
                TextColumn::make('type')->label('Document')
                    ->formatStateUsing(fn ($state) => DocumentRequest::$typeLabels[$state] ?? $state)
                    ->badge()->color('info'),
                TextColumn::make('format')->label('Format')->badge()
                    ->color(fn ($state) => $state === 'digital' ? 'primary' : 'gray')
                    ->formatStateUsing(fn ($state) => $state === 'digital' ? 'PDF' : 'Papier'),
                TextColumn::make('status')->label('Statut')->badge()
                    ->color(fn ($state) => match ($state) {
                        'en_attente' => 'warning', 'traité' => 'success', 'refusé' => 'danger', default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Demandé le')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Entreprise')->options(Company::pluck('name', 'id')),
                SelectFilter::make('status')->label('Statut')->options(['en_attente' => 'En attente', 'traité' => 'Traité', 'refusé' => 'Refusé']),
                SelectFilter::make('format')->label('Format')->options(['digital' => 'Digitale (PDF)', 'papier' => 'Papier']),
            ])
            ->actions([
                Action::make('approve_pdf')
                    ->label('Télécharger PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->visible(fn (DocumentRequest $record) => $record->format === 'digital')
                    ->url(fn (DocumentRequest $record) => route('documents.pdf', $record))
                    ->openUrlInNewTab(),

                Action::make('mark_printed')
                    ->label('Marquer imprimé')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn (DocumentRequest $record) => $record->format === 'papier' && $record->status === 'en_attente')
                    ->requiresConfirmation()
                    ->action(fn (DocumentRequest $record) => $record->update([
                        'status'       => 'traité',
                        'processed_by' => auth()->id(),
                        'processed_at' => now(),
                    ])),

                ActionGroup::make([
                    Action::make('preview_pdf')
                        ->label('Aperçu PDF')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->visible(fn (DocumentRequest $record) => $record->format === 'digital')
                        ->url(fn (DocumentRequest $record) => route('documents.preview', $record))
                        ->openUrlInNewTab(),

                    Action::make('refuse')
                        ->label('Refuser')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (DocumentRequest $record) => $record->status === 'en_attente')
                        ->requiresConfirmation()
                        ->action(fn (DocumentRequest $record) => $record->update([
                            'status'       => 'refusé',
                            'processed_by' => auth()->id(),
                            'processed_at' => now(),
                        ])),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocumentRequests::route('/'),
            'create' => Pages\CreateDocumentRequest::route('/create'),
            'edit'   => Pages\EditDocumentRequest::route('/{record}/edit'),
        ];
    }
}
