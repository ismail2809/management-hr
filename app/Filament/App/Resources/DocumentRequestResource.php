<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\DocumentRequestResource\Pages;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
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

class DocumentRequestResource extends Resource
{
    use HasCompanyField;
    protected static ?string $model = DocumentRequest::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationLabel = 'Demandes';
    protected static ?string $modelLabel = 'Demande';
    protected static \UnitEnum|string|null $navigationGroup = 'Demandes';
    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->hasRole('employee') ? 'Mes demandes' : 'Demandes';
    }

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()?->hasRole('employee') ? 'Mes demandes' : 'Demandes';
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

    public static function canForceDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canForceDeleteAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasRole('employee')) {
            $query->where('employee_id', auth()->user()->employee_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        $isEmployee = auth()->user()?->hasRole('employee');

        return $schema->columns(1)->components([
            Section::make('Demandeur')
                ->description('Sélectionnez l\'employé concerné par cette demande.')
                ->icon('heroicon-o-user-circle')
                ->columns(2)
                ->schema([
                    static::companyField(),

                    Section::make('Employé(e)s')
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

            Section::make('Type de demande')
                ->description('Choisissez la catégorie de votre demande.')
                ->icon('heroicon-o-tag')
                ->schema([
                    Radio::make('categorie')
                        ->label('Catégorie de demande')
                        ->options(['document' => 'Document administratif', 'autre' => 'Autre demande'])
                        ->default('document')
                        ->inline()
                        ->required()
                        ->live(),
                ]),

            Section::make('Document administratif')
                ->visible(fn ($get) => $get('categorie') === 'document')
                ->schema([
                    Select::make('type')
                        ->label('Type de document')
                        ->options(fn () => DocumentType::where('active', true)->where('categorie', 'document')->orderBy('sort_order')->pluck('name', 'code')->toArray() ?: DocumentRequest::$documentTypes)
                        ->required(fn ($get) => $get('categorie') === 'document'),

                    Radio::make('format')
                        ->label('Format souhaité')
                        ->options(['digital' => 'Version digitale (PDF)', 'papier' => 'Version papier'])
                        ->default('digital')
                        ->inline(),
                ]),

            Section::make('Autre demande')
                ->visible(fn ($get) => $get('categorie') === 'autre')
                ->schema([
                    Select::make('type')
                        ->label('Type de demande')
                        ->options(fn () => DocumentType::where('active', true)->where('categorie', 'autre')->orderBy('sort_order')->pluck('name', 'code')->toArray() ?: DocumentRequest::$autreTypes)
                        ->required(fn ($get) => $get('categorie') === 'autre'),
                ]),

            Section::make('Détails')->schema([
                Textarea::make('description')
                    ->label('Description / détails')
                    ->rows(3)
                    ->nullable(),

                Textarea::make('reason')
                    ->label('Remarques complémentaires')
                    ->rows(2)
                    ->nullable(),

                Select::make('status')
                    ->label('Statut')
                    ->options(['en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé'])
                    ->default('en_attente')
                    ->disabled($isEmployee)
                    ->dehydrated()
                    ->required(),

                FileUpload::make('fichier_final')
                    ->label('Fichier final (uploadé par l\'admin)')
                    ->disk('public')
                    ->directory('document-requests/finals')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(10240)
                    ->nullable()
                    ->hidden($isEmployee),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->searchable(['employees.first_name', 'employees.last_name'])
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('categorie')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn ($state) => $state === 'document' ? 'primary' : 'warning')
                    ->formatStateUsing(fn ($state) => $state === 'document' ? 'Document' : 'Autre'),

                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => DocumentRequest::$documentTypes[$state] ?? DocumentRequest::$autreTypes[$state] ?? $state)
                    ->badge()
                    ->color('info'),

                TextColumn::make('format')
                    ->label('Format')
                    ->badge()
                    ->color(fn ($state) => $state === 'digital' ? 'primary' : 'gray')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'digital' => 'PDF', 'papier' => 'Papier', default => '—',
                    }),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'en_attente' => 'warning',
                        'approuvé'   => 'success',
                        'refusé'     => 'danger',
                        default      => 'gray',
                    }),

                TextColumn::make('nb_telechargements')
                    ->label('Téléchargements')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Demandé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('categorie')->label('Catégorie')->options(['document' => 'Document', 'autre' => 'Autre']),
                SelectFilter::make('status')->label('Statut')->options(['en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé']),
                SelectFilter::make('format')->label('Format')->options(['digital' => 'Digitale', 'papier' => 'Papier']),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Voir'),

                    Action::make('download_final')
                        ->label('Télécharger document')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('primary')
                        ->visible(fn (DocumentRequest $record) => $record->status === 'approuvé' && $record->fichier_final)
                        ->url(fn (DocumentRequest $record) => asset('storage/' . $record->fichier_final))
                        ->openUrlInNewTab()
                        ->action(function (DocumentRequest $record) {
                            $record->increment('nb_telechargements');
                        }),

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
            'index'  => Pages\ListDocumentRequests::route('/'),
            'create' => Pages\CreateDocumentRequest::route('/create'),
            'view'   => Pages\ViewDocumentRequest::route('/{record}'),
            'edit'   => Pages\EditDocumentRequest::route('/{record}/edit'),
        ];
    }
}
