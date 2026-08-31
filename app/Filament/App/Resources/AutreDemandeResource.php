<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\AutreDemandeResource\Pages;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Groupe;
use App\Models\NatureDocument;
use App\Models\NiveauScolaire;
use App\Models\Profession;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                ->columns(2)
                ->schema([
                    Select::make('type')
                        ->label('Type de demande')
                        ->options(fn () => DocumentType::where('active', true)->where('categorie', 'autre')->orderBy('sort_order')->pluck('name', 'code')->toArray() ?: DocumentRequest::$autreTypes)
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                ]),

            Section::make('Détails Photocopie')
                ->icon('heroicon-o-document-duplicate')
                ->columns(2)
                ->visible(fn (Get $get) => $get('type') === 'photocopie')
                ->description(fn () => sprintf(
                    'La demande doit être déposée au moins %d jour(s) avant la date souhaitée.',
                    config('hr.photocopie_delay_days', 3)
                ))
                ->schema([
                    Select::make('photocopie_sous_type')
                        ->label('Nature du document')
                        ->options(fn () => NatureDocument::where('active', true)
                            ->orderBy('sort_order')
                            ->pluck('name', 'name')
                            ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull(),

                    Select::make('photocopie_niveau')
                        ->label('Niveau')
                        ->options(fn () => NiveauScolaire::orderBy('order')->pluck('name', 'name')->toArray())
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('photocopie_groupe', null))
                        ->required(),

                    Select::make('photocopie_groupe')
                        ->label('Groupe / Classe')
                        ->options(function (Get $get) {
                            $niveauName = $get('photocopie_niveau');
                            if (! $niveauName) {
                                return [];
                            }
                            $niveau = NiveauScolaire::where('name', $niveauName)->first();
                            if (! $niveau) {
                                return [];
                            }
                            return Groupe::where('niveau_scolaire_id', $niveau->id)
                                ->orderBy('name')
                                ->pluck('name', 'name')
                                ->toArray();
                        })
                        ->searchable()
                        ->required()
                        ->disabled(fn (Get $get) => ! $get('photocopie_niveau')),

                    TextInput::make('photocopie_nb_copies')
                        ->label('Nombre de copies souhaitées')
                        ->numeric()
                        ->minValue(1)
                        ->required(),

                    DatePicker::make('photocopie_date_souhaitee')
                        ->label('Date souhaitée')
                        ->minDate(fn () => now()->addDays(config('hr.photocopie_delay_days', 3))->toDateString())
                        ->hint(fn () => sprintf('Minimum %d jour(s) à l\'avance', config('hr.photocopie_delay_days', 3)))
                        ->hintIcon('heroicon-o-clock')
                        ->hintColor('warning')
                        ->required(),
                ]),

            Section::make('Participants — Rencontre direction')
                ->icon('heroicon-o-users')
                ->visible(fn (Get $get) => $get('type') === 'rencontre_direction')
                ->schema([
                    Select::make('rencontre_employee_ids')
                        ->label('Employés concernés')
                        ->multiple()
                        ->options(function () {
                            $excluded = config('hr.rencontre_direction_excluded_professions', []);

                            $excludedIds = Profession::withoutGlobalScopes()
                                ->whereIn('name', $excluded)
                                ->pluck('id');

                            return Employee::with('profession')
                                ->whereNotIn('profession_id', $excludedIds)
                                ->orWhereNull('profession_id')
                                ->get()
                                ->mapWithKeys(fn (Employee $e) => [
                                    $e->id => $e->full_name . ($e->profession ? ' — ' . $e->profession->name : ''),
                                ])
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpanFull(),
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
                    ->label('Fichier')
                    ->disk('public')
                    ->directory('document-requests/finals')
                    ->acceptedFileTypes([
                        'application/pdf', 'image/jpeg', 'image/png', 'image/webp',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(10240)
                    ->nullable()
                    ->disabled($isEmployee)
                    ->dehydrated(! $isEmployee),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Demandeur')
                ->icon('heroicon-o-user-circle')
                ->columns(2)
                ->schema([
                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'en_attente' => 'warning',
                            'approuvé'   => 'success',
                            'refusé'     => 'danger',
                            default      => 'gray',
                        }),
                    TextEntry::make('created_at')->label('Demandé le')->date('d/m/Y'),
                    TextEntry::make('employee.full_name')->label('Employé(e)'),
                    TextEntry::make('type')
                        ->label('Type de demande')
                        ->formatStateUsing(fn ($state) => DocumentRequest::$autreTypes[$state]
                            ?? DocumentType::withoutGlobalScopes()->where('code', $state)->value('name')
                            ?? $state)
                        ->badge()
                        ->color('warning'),
                ]),

            Section::make('Détails Photocopie')
                ->icon('heroicon-o-document-duplicate')
                ->columns(2)
                ->hidden(fn (DocumentRequest $record) => $record->type !== 'photocopie')
                ->schema([
                    TextEntry::make('photocopie_sous_type')->label('Nature du document')->columnSpanFull(),
                    TextEntry::make('photocopie_niveau')->label('Niveau'),
                    TextEntry::make('photocopie_groupe')->label('Groupe / Classe'),
                    TextEntry::make('photocopie_nb_copies')->label('Nombre de copies'),
                    TextEntry::make('photocopie_date_souhaitee')->label('Date souhaitée')->date('d/m/Y'),
                ]),

            Section::make('Participants — Rencontre direction')
                ->icon('heroicon-o-users')
                ->hidden(fn (DocumentRequest $record) => $record->type !== 'rencontre_direction')
                ->schema([
                    TextEntry::make('rencontre_employee_ids')
                        ->label('Employés concernés')
                        ->html()
                        ->formatStateUsing(function ($state, DocumentRequest $record): string {
                            if (empty($state)) {
                                return '—';
                            }
                            return Employee::withoutGlobalScopes()
                                ->with('profession')
                                ->whereIn('id', $state)
                                ->get()
                                ->map(fn (Employee $e) => e($e->full_name) . ($e->profession ? ' <span class="text-gray-400">— ' . e($e->profession->name) . '</span>' : ''))
                                ->join('<br>');
                        }),
                ]),

            Section::make('Détails')
                ->columns(2)
                ->schema([
                    TextEntry::make('description')->label('Description / détails')->columnSpanFull(),
                    TextEntry::make('reason')->label('Remarques complémentaires')->columnSpanFull(),
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
                    ->label('Type de demande')
                    ->formatStateUsing(fn ($state) => DocumentRequest::$autreTypes[$state]
                        ?? DocumentType::withoutGlobalScopes()->where('code', $state)->value('name')
                        ?? $state)
                    ->badge()
                    ->color('warning'),

TextColumn::make('fichier_final')
                    ->label('Fichier')
                    ->formatStateUsing(fn ($state) => $state ? 'Télécharger' : '—')
                    ->icon(fn ($state) => $state ? 'heroicon-o-paper-clip' : null)
                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                    ->url(fn (DocumentRequest $record) => $record->fichier_final ? asset('storage/' . $record->fichier_final) : null)
                    ->openUrlInNewTab(),

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
