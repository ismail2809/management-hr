<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\AutreDemandeResource\Pages;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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

class AutreDemandeResource extends Resource
{
    use HasCompanyField;

    protected static ?string $model = DocumentRequest::class;
    protected static ?string $slug = 'autres-demandes';
    protected static ?string $modelLabel = 'Autre demande';
    protected static ?string $pluralModelLabel = 'Autres demandes';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static \UnitEnum|string|null $navigationGroup = 'Demandes';
    protected static ?int $navigationSort = 12;

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->hasRole('employee') ? 'Mes autres demandes' : 'Autres demandes';
    }

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()?->hasRole('employee') ? 'Mes demandes' : 'Demandes';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('categorie', 'autre');

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
            Hidden::make('categorie')->default('autre'),

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

            Section::make('Autre demande')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    Select::make('type')
                        ->label('Type de demande')
                        ->options(fn () => DocumentType::where('active', true)->where('categorie', 'autre')->orderBy('sort_order')->pluck('name', 'code')->toArray() ?: DocumentRequest::$autreTypes)
                        ->required(),
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
                        'application/pdf', 'image/jpeg', 'image/png', 'image/webp',
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

                TextColumn::make('type')
                    ->label('Type de demande')
                    ->formatStateUsing(fn ($state) => DocumentRequest::$autreTypes[$state] ?? $state)
                    ->badge()
                    ->color('warning'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'en_attente' => 'warning',
                        'approuvé'   => 'success',
                        'refusé'     => 'danger',
                        default      => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Demandé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(['en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé']),
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
            'index'  => Pages\ListAutreDemandes::route('/'),
            'create' => Pages\CreateAutreDemande::route('/create'),
            'view'   => Pages\ViewAutreDemande::route('/{record}'),
            'edit'   => Pages\EditAutreDemande::route('/{record}/edit'),
        ];
    }
}
