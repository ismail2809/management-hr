<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasCompanyField;
use App\Filament\Admin\Resources\AutreDemandeResource\Pages;
use App\Models\AnneeScolaire;
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
use App\Rules\SafeFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\HtmlString;
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

    public static function getNavigationBadge(): ?string
    {
        $user  = auth()->user();
        $query = static::getModel()::where('categorie', 'autre')->where('status', 'en_attente');
        if ($user?->isBasicRole()) {
            $query->where('employee_id', $user->employee_id);
        }
        return (string) $query->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
    protected static ?int $navigationSort = 12;

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->isBasicRole() ? 'Mes autres demandes' : 'Autres demandes';
    }

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()?->isBasicRole() ? 'Mes demandes' : 'Demandes';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('categorie', 'autre');

        if (auth()->user()?->isBasicRole()) {
            $query->where('employee_id', auth()->user()->employee_id);
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        // Admins, secretaire, surveillante : oui (toutes les demandes)
        // Extended roles (enseignant, etc.) : oui (leurs propres demandes)
        // Basic roles (femme-de-menage, chauffeur, gardien, employee) : non
        return ! $user?->isBasicRole() || $user?->isExtendedRole();
    }

    public static function canCreate(): bool
    {
        // Secretaire : vue seule sur Autres Demandes
        return ! auth()->user()?->hasRole('secretaire');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        // Employés : peuvent modifier uniquement leur propre demande en attente
        if ($user?->isBasicRole()) {
            return $record->employee_id === $user->employee_id && $record->status === 'en_attente';
        }
        // Secretaire : vue seule
        return $user?->hasAnyRole(['super-admin', 'directeur', 'surveillante']);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->hasRole('super-admin');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasRole('super-admin');
    }

    public static function form(Schema $schema): Schema
    {
        $isEmployee = auth()->user()?->isBasicRole();

        return $schema->columns(1)->components([
            Hidden::make('categorie')->default('autre'),

            Section::make('Demandeur')
                ->description('Sélectionnez l\'employé concerné par cette demande.')
                ->icon('heroicon-o-user-circle')
                ->compact()
                ->schema([
                    static::companyField(),

                    Grid::make(2)->schema([
                        Select::make('employee_id')
                            ->label('Employé(e)')
                            ->relationship('employee', 'first_name', fn ($query) => $query->with('profession'))
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name . ($record->profession ? ' — ' . $record->profession->name : ''))
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->user()?->employee_id)
                            ->disabled($isEmployee)
                            ->dehydrated()
                            ->required(),

                        Select::make('status')
                            ->label('Statut')
                            ->options(['en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé'])
                            ->default('en_attente')
                            ->disabled($isEmployee)
                            ->dehydrated()
                            ->required(),
                    ]),
                ]),

            Section::make('Autre demande')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->columns(2)
                ->schema([
                    Select::make('annee_scolaire')
                        ->label('Année scolaire')
                        ->options(fn () => AnneeScolaire::orderByDesc('name')->pluck('name', 'name')->toArray())
                        ->default(fn () => AnneeScolaire::where('is_active', true)->value('name'))
                        ->required()
                        ->native(false),

                    Select::make('type')
                        ->label('Type de demande')
                        ->options(fn () => DocumentType::where('active', true)->where('categorie', 'autre')->orderBy('sort_order')->pluck('name', 'code')->toArray() ?: DocumentRequest::$autreTypes)
                        ->required()
                        ->live(),
                    DatePicker::make('date_souhaitee')
                        ->label('Date souhaitée')
                        ->required(fn (Get $get) => $get('type') && $get('type') !== 'photocopie')
                        ->hidden(fn (Get $get) => $get('type') === 'photocopie')
                        ->native(false)
                        ->minDate(now()),
                ]),

            Section::make('Détails Photocopie')
                ->icon('heroicon-o-document-duplicate')
                ->columns(2)
                ->visible(fn (Get $get) => $get('type') === 'photocopie')
                ->description(fn () => sprintf(
                    'La demande doit être déposée au moins %d jour(s) ouvré(s) à l\'avance (week-ends exclus).',
                    config('hr.photocopie_delay_days', 3)
                ))
                ->schema([
                    Select::make('photocopie_sous_type')
                        ->label('Nature du document')
                        ->options(fn () => NatureDocument::withoutGlobalScopes()
                            ->where('active', true)
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
                        ->maxValue(config('hr.photocopie_max_copies_per_day', 300))
                        ->hint(fn () => sprintf('Maximum %d copies par jour', config('hr.photocopie_max_copies_per_day', 300)))
                        ->hintIcon('heroicon-o-printer')
                        ->hintColor('warning')
                        ->required(),

                    DatePicker::make('photocopie_date_souhaitee')
                        ->label('Date souhaitée')
                        ->minDate(function () {
                            $days = config('hr.photocopie_delay_days', 3);
                            $date = now()->copy();
                            // Le jour de la demande compte comme jour 1 s'il est ouvré
                            $added = $date->isWeekend() ? 0 : 1;
                            while ($added < $days) {
                                $date->addDay();
                                if (! $date->isWeekend()) {
                                    $added++;
                                }
                            }
                            return $date->toDateString();
                        })
                        ->hint(fn () => sprintf('Minimum %d jour(s) ouvré(s) à l\'avance (week-ends exclus)', config('hr.photocopie_delay_days', 3)))
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
                                ->where(fn ($q) => $q->whereNotIn('profession_id', $excludedIds)->orWhereNull('profession_id'))
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
                Textarea::make('description')
                    ->label('Description / détails')
                    ->rows(3)
                    ->required(),

                FileUpload::make('fichier_joint')
                    ->label('Pièce jointe (optionnel)')
                    ->disk('public')
                    ->directory('document-requests/joints')
                    ->acceptedFileTypes([
                        'application/pdf', 'image/jpeg', 'image/png', 'image/webp',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ])
                    ->maxSize(10240)
                    ->rules([new SafeFileUpload()])
                    ->nullable()
                    ->visible($isEmployee),

                Placeholder::make('fichier_joint_view')
                    ->label('Pièce jointe de l\'employé')
                    ->visible(! $isEmployee)
                    ->content(fn ($record) => $record?->fichier_joint
                        ? new HtmlString('<a href="' . asset('storage/' . $record->fichier_joint) . '" target="_blank" class="text-primary-600 hover:underline font-medium">Ouvrir le fichier</a>')
                        : new HtmlString('<span class="text-gray-400">Aucun fichier joint</span>')
                    ),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->searchable(['employees.first_name', 'employees.last_name'])
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('annee_scolaire')->label('Année scolaire')->sortable()->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type')
                    ->label('Type de demande')
                    ->formatStateUsing(fn ($state) => DocumentRequest::$autreTypes[$state]
                        ?? DocumentType::withoutGlobalScopes()->where('code', $state)->value('name')
                        ?? $state)
                    ->badge()
                    ->color('warning'),

                TextColumn::make('date_souhaitee')
                    ->label('Date souhaitée')
                    ->getStateUsing(fn ($record) => $record->date_souhaitee ?? $record->photocopie_date_souhaitee)
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : '—')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw(
                            'COALESCE(date_souhaitee, photocopie_date_souhaitee) ' . $direction
                        );
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

                TextColumn::make('fichier_joint')
                    ->label('Pièce jointe')
                    ->default('—')
                    ->formatStateUsing(fn ($state) => $state && $state !== '—' ? 'Voir pièce jointe' : '—')
                    ->icon(fn ($state) => $state && $state !== '—' ? 'heroicon-o-paper-clip' : null)
                    ->color(fn ($state) => $state && $state !== '—' ? 'info' : 'gray')
                    ->url(fn (DocumentRequest $record) => $record->fichier_joint ? asset('storage/' . $record->fichier_joint) : null)
                    ->openUrlInNewTab(),

                TextColumn::make('created_at')
                    ->label('Demandé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('annee_scolaire')->label('Année scolaire')
                    ->options(fn () => AnneeScolaire::orderByDesc('name')->pluck('name', 'name')->toArray()),
                SelectFilter::make('status')->label('Statut')->options(['en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé']),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Voir'),

                    Action::make('download_joint')
                        ->label('Voir pièce jointe')
                        ->icon('heroicon-o-paper-clip')
                        ->color('info')
                        ->visible(fn (DocumentRequest $record) => (bool) $record->fichier_joint)
                        ->url(fn (DocumentRequest $record) => asset('storage/' . $record->fichier_joint))
                        ->openUrlInNewTab(),

                    Action::make('approve')
                        ->label('Approuver')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (DocumentRequest $record) => $record->status === 'en_attente' && ! auth()->user()?->isBasicRole())
                        ->requiresConfirmation()
                        ->action(fn (DocumentRequest $record) => $record->update([
                            'status' => 'approuvé', 'processed_by' => auth()->id(), 'processed_at' => now(),
                        ])),

                    Action::make('refuse')
                        ->label('Refuser')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (DocumentRequest $record) => $record->status === 'en_attente' && ! auth()->user()?->isBasicRole())
                        ->requiresConfirmation()
                        ->action(fn (DocumentRequest $record) => $record->update([
                            'status' => 'refusé', 'processed_by' => auth()->id(), 'processed_at' => now(),
                        ])),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()->visible(fn () => auth()->user()?->hasRole('super-admin'))])])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Vérifie qu'il n'existe pas déjà une demande de photocopie de nature
     * conflictuelle (Examen / Contrôle) pour le même groupe et la même date.
     * Lance une ValidationException avec l'erreur positionnée sur le champ date.
     */
    public static function checkPhotocopieConflict(array $data, ?int $excludeId = null): void
    {
        $conflicting       = config('hr.photocopie_conflicting_natures', ['Examen', 'Contrôle continu', "Contrôle d'essai"]);
        $conflictNiveaux   = config('hr.photocopie_conflict_niveaux', ['Primaire']);

        // ── Vérification du quota journalier de copies ───────────────────────
        $maxCopies = config('hr.photocopie_max_copies_per_day', 300);
        if (
            ($data['type'] ?? null) === 'photocopie' &&
            ! empty($data['photocopie_date_souhaitee']) &&
            ! empty($data['photocopie_nb_copies'])
        ) {
            $query = \App\Models\DocumentRequest::where('categorie', 'autre')
                ->where('type', 'photocopie')
                ->where('photocopie_date_souhaitee', $data['photocopie_date_souhaitee'])
                ->where('status', '!=', 'refusé');

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            $existingTotal = (int) $query->sum('photocopie_nb_copies');
            $newTotal      = $existingTotal + (int) $data['photocopie_nb_copies'];

            if ($newTotal > $maxCopies) {
                $remaining     = max(0, $maxCopies - $existingTotal);
                $dateFormatted = \Carbon\Carbon::parse($data['photocopie_date_souhaitee'])->translatedFormat('l d/m/Y');
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'data.photocopie_nb_copies' => sprintf(
                        'Le quota journalier de %d copies est dépassé pour le %s. Copies restantes disponibles : %d.',
                        $maxCopies,
                        $dateFormatted,
                        $remaining
                    ),
                ]);
            }
        }

        if (
            ($data['type'] ?? null) !== 'photocopie' ||
            ! in_array($data['photocopie_sous_type'] ?? null, $conflicting) ||
            empty($data['photocopie_groupe']) ||
            empty($data['photocopie_date_souhaitee'])
        ) {
            return;
        }

        // Vérifier que le niveau appartient aux niveaux soumis à la restriction
        $niveau = $data['photocopie_niveau'] ?? null;
        if ($niveau) {
            $match = collect($conflictNiveaux)->contains(
                fn ($n) => str_contains(mb_strtolower($niveau), mb_strtolower($n))
            );
            if (! $match) {
                return;
            }
        }

        $query = \App\Models\DocumentRequest::where('categorie', 'autre')
            ->where('type', 'photocopie')
            ->where('photocopie_groupe', $data['photocopie_groupe'])
            ->where('photocopie_date_souhaitee', $data['photocopie_date_souhaitee'])
            ->whereIn('photocopie_sous_type', $conflicting)
            ->where('status', '!=', 'refusé');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $existing = $query->first();

        if (! $existing) {
            return;
        }

        $dateFormatted = \Carbon\Carbon::parse($data['photocopie_date_souhaitee'])->translatedFormat('l d/m/Y');

        throw \Illuminate\Validation\ValidationException::withMessages([
            'data.photocopie_date_souhaitee' => sprintf(
                'Le groupe "%s" a déjà une demande "%s" planifiée le %s. Veuillez choisir un autre jour.',
                $data['photocopie_groupe'],
                $existing->photocopie_sous_type,
                $dateFormatted
            ),
        ]);
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
