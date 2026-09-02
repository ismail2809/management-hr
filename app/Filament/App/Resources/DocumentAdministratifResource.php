<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\DocumentAdministratifResource\Pages;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DocumentAdministratifResource extends Resource
{
    use HasCompanyField;

    protected static ?string $model = DocumentRequest::class;
    protected static ?string $slug = 'documents-administratifs';
    protected static ?string $modelLabel = 'Document administratif';
    protected static ?string $pluralModelLabel = 'Documents administratifs';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static \UnitEnum|string|null $navigationGroup = 'Demandes';
    protected static ?int $navigationSort = 11;

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->hasRole('employee') ? 'Mes documents' : 'Documents administratifs';
    }

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()?->hasRole('employee') ? 'Mes demandes' : 'Demandes';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('categorie', 'document');

        if (auth()->user()?->hasRole('employee')) {
            $query->where('employee_id', auth()->user()->employee_id);
        }

        return $query;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'directeur']);
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'directeur']);
    }

    public static function form(Schema $schema): Schema
    {
        $isEmployee = auth()->user()?->hasRole('employee');

        return $schema->columns(1)->components([
            Hidden::make('categorie')->default('document'),

            Section::make('Demandeur')
                ->description('Sélectionnez l\'employé concerné par cette demande.')
                ->icon('heroicon-o-user-circle')
                ->columns(2)
                ->schema([
                    static::companyField(),

                    Section::make('Employé(e)')
                        ->icon('heroicon-o-user')
                        ->compact()
                        ->schema([
                            Select::make('employee_id')
                                ->label('Employé(e)')
                                ->relationship('employee', 'first_name')
                                ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                                ->searchable()
                                ->preload()
                                ->default(fn () => auth()->user()?->employee_id)
                                ->disabled($isEmployee)
                                ->dehydrated()
                                ->required()
                                ->columnSpanFull(),
                        ]),
                ]),

            Section::make('Document administratif')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Select::make('type')
                        ->label('Type de document')
                        ->options(fn () => DocumentType::where('active', true)->where('categorie', 'document')->orderBy('sort_order')->pluck('name', 'code')->toArray() ?: DocumentRequest::$documentTypes)
                        ->required(),

                    Radio::make('format')
                        ->label('Format souhaité')
                        ->options(['digital' => 'Version digitale (PDF)', 'papier' => 'Version papier'])
                        ->default('digital')
                        ->inline(),
                ]),

            Section::make('Détails')->schema([
                Select::make('status')
                    ->label('Statut')
                    ->options(['en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé'])
                    ->default('en_attente')
                    ->disabled($isEmployee)
                    ->dehydrated()
                    ->required(),

                Textarea::make('description')
                    ->label('Description / détails')
                    ->rows(3)
                    ->nullable(),

                Textarea::make('reason')
                    ->label('Remarques complémentaires')
                    ->rows(2)
                    ->nullable(),

                FileUpload::make('fichier_final')
                    ->label('Fichier final (uploadé par l\'admin)')
                    ->disk('public')
                    ->directory('document-requests/finals')
                    ->acceptedFileTypes([
                        'application/pdf', 'image/jpeg', 'image/png', 'image/webp',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(10240)
                    ->nullable()
                    ->openable()
                    ->downloadable()
                    ->previewable()
                    ->hidden($isEmployee),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'en_attente' => 'warning',
                        'approuvé'   => 'success',
                        'refusé'     => 'danger',
                        default      => 'gray',
                    }),

                TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->searchable(['employees.first_name', 'employees.last_name'])
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('type')
                    ->label('Type de document')
                    ->formatStateUsing(fn ($state) => DocumentRequest::$documentTypes[$state] ?? $state)
                    ->badge()
                    ->color('info'),

                TextColumn::make('format')
                    ->label('Format')
                    ->badge()
                    ->color(fn ($state) => $state === 'digital' ? 'primary' : 'gray')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'digital' => 'PDF', 'papier' => 'Papier', default => '—',
                    }),

                TextColumn::make('created_at')
                    ->label('Demandé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(['en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé']),
                SelectFilter::make('format')->label('Format')->options(['digital' => 'Digitale', 'papier' => 'Papier']),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Voir'),

                    Action::make('apercu_pdf')
                        ->label('Aperçu PDF')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->color('info')
                        ->visible(fn (DocumentRequest $record) => view()->exists('pdf.documents.' . $record->type))
                        ->url(fn (DocumentRequest $record) => route('documents.preview', $record))
                        ->openUrlInNewTab(),

                    Action::make('generer_pdf')
                        ->label('Télécharger PDF généré')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->visible(fn (DocumentRequest $record) => view()->exists('pdf.documents.' . $record->type) && ! auth()->user()?->hasRole('employee'))
                        ->url(fn (DocumentRequest $record) => route('documents.pdf', $record))
                        ->openUrlInNewTab(),

                    Action::make('voir_fichier')
                        ->label('Voir fichier uploadé')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->visible(fn (DocumentRequest $record) => filled($record->fichier_final))
                        ->url(fn (DocumentRequest $record) => asset('storage/' . $record->fichier_final))
                        ->openUrlInNewTab(),

                    Action::make('download_final')
                        ->label('Télécharger fichier uploadé')
                        ->icon('heroicon-o-paper-clip')
                        ->color('primary')
                        ->visible(fn (DocumentRequest $record) => $record->status === 'approuvé' && $record->fichier_final)
                        ->url(fn (DocumentRequest $record) => asset('storage/' . $record->fichier_final))
                        ->openUrlInNewTab()
                        ->action(fn (DocumentRequest $record) => $record->increment('nb_telechargements')),

                    Action::make('approve')
                        ->label('Approuver')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (DocumentRequest $record) => $record->status === 'en_attente' && ! auth()->user()?->hasRole('employee'))
                        ->requiresConfirmation()
                        ->action(fn (DocumentRequest $record) => $record->update([
                            'status' => 'approuvé', 'processed_by' => auth()->id(), 'processed_at' => now(),
                        ])),

                    Action::make('refuse')
                        ->label('Refuser')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (DocumentRequest $record) => $record->status === 'en_attente' && ! auth()->user()?->hasRole('employee'))
                        ->requiresConfirmation()
                        ->action(fn (DocumentRequest $record) => $record->update([
                            'status' => 'refusé', 'processed_by' => auth()->id(), 'processed_at' => now(),
                        ])),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDocumentAdministratifs::route('/'),
            'create' => Pages\CreateDocumentAdministratif::route('/create'),
            'view'   => Pages\ViewDocumentAdministratif::route('/{record}'),
            'edit'   => Pages\EditDocumentAdministratif::route('/{record}/edit'),
        ];
    }
}
