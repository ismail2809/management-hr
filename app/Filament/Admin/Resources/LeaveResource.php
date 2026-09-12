<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasCompanyField;
use App\Filament\Admin\Resources\LeaveResource\Pages;
use App\Models\CommunicationMethod;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeaveResource extends Resource
{
    use HasCompanyField;
    protected static ?string $model = Leave::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Absences / Congés';

    public static function getNavigationBadge(): ?string
    {
        $user  = auth()->user();
        $query = static::getModel()::where('status', 'en_attente');
        if ($user?->isBasicRole()) {
            $query->where('employee_id', $user->employee_id);
        }
        return (string) $query->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
    protected static ?string $modelLabel = 'Demande d\'absence';
    protected static ?string $pluralModelLabel = 'Demandes d\'absences';
    protected static \UnitEnum|string|null $navigationGroup = 'Congés & Absences';
    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->isBasicRole() ? 'Demandes d\'absences' : 'Absences / Congés';
    }

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()?->isBasicRole() ? 'Mes demandes' : 'Congés & Présence';
    }

    public static function getNavigationSort(): ?int
    {
        return auth()->user()?->isBasicRole() ? 1 : 7;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->hasRole('super-admin');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasRole('super-admin');
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

        if (auth()->user()?->isBasicRole()) {
            $query->where('employee_id', auth()->user()->employee_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        $isEmployee = auth()->user()?->isBasicRole();

        return $schema->columns(1)->components([

            // ── Section principale ────────────────────────────────────────────
            Section::make('Demande d\'absence')
                ->description('Renseignez les informations pour soumettre une nouvelle demande.')
                ->icon('heroicon-o-calendar-days')
                ->columns(2)
                ->schema([
                    Section::make('Demandeur')
                        ->icon('heroicon-o-user-circle')
                        ->compact()
                        ->columnSpanFull()
                        ->columns(3)
                        ->schema([
                            Select::make('employee_id')
                                ->label('Employé(e)')
                                ->relationship('employee', 'first_name', fn ($query) => $query->with('profession'))
                                ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name . ($record->profession ? ' — ' . $record->profession->name : ''))
                                ->searchable()
                                ->preload()
                                ->default(fn () => auth()->user()?->employee_id)
                                ->disabled($isEmployee)
                                ->dehydrated()
                                ->required()
                                ->live(),

                            Select::make('categorie')
                                ->label('Catégorie')
                                ->options(['conge' => 'Congé', 'absence' => 'Absence'])
                                ->default('conge')
                                ->required()
                                ->live(),

                            Select::make('leave_type_id')
                                ->label('Type de congé')
                                ->options(fn () => LeaveType::orderBy('name')->pluck('name', 'id')->toArray())
                                ->searchable()
                                ->placeholder('Sélectionner…')
                                ->nullable()
                                ->visible(fn ($get) => $get('categorie') === 'conge'),
                        ]),

                    static::companyField()->columns(2),

                    Section::make('Période')
                        ->icon('heroicon-o-clock')
                        ->compact()
                        ->columnSpanFull()
                        ->columns(1)
                        ->schema([
                            Grid::make(3)->schema([
                                DatePicker::make('start_date')
                                    ->label('Date de début')
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),

                                DatePicker::make('end_date')
                                    ->label('Date de fin')
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->minDate(fn ($get) => $get('start_date')),

                                Placeholder::make('duree_calculee')
                                    ->label('Durée estimée')
                                    ->content(function ($get): string {
                                        $start = $get('start_date');
                                        $end   = $get('end_date');
                                        if (! $start || ! $end) return '—';
                                        try {
                                            $days = Carbon::parse($start)->diffInWeekdays(Carbon::parse($end)) + 1;
                                            return $days . ' jour' . ($days > 1 ? 's' : '') . ' ouvrable' . ($days > 1 ? 's' : '');
                                        } catch (\Exception) {
                                            return '—';
                                        }
                                    }),
                            ]),

                            Textarea::make('reason')
                                ->label('Motif')
                                ->placeholder('Raison de la demande…')
                                ->rows(2)
                                ->nullable()
                                ->columnSpanFull(),
                        ]),
                ]),

            // ── Options avancées (repliées) ───────────────────────────────────
            Section::make('Options')
                ->icon('heroicon-o-adjustments-horizontal')
                ->schema([
                    Grid::make(3)
                        ->hidden($isEmployee)
                        ->schema([
                            Select::make('status')
                                ->label('Statut')
                                ->options([
                                    'en_attente' => 'En attente',
                                    'approuvé'   => 'Approuvé',
                                    'refusé'     => 'Refusé',
                                ])
                                ->default('en_attente')
                                ->required(),

                            Select::make('communication_method')
                                ->label('Mode de communication')
                                ->options(fn () => CommunicationMethod::where('active', true)->orderBy('sort_order')->pluck('name', 'code')->toArray())
                                ->placeholder('Choisir un mode…')
                                ->nullable()
                                ->native(false),

                            DateTimePicker::make('appointment_date')
                                ->label('Date de rendez-vous')
                                ->seconds(false)
                                ->native(false)
                                ->nullable(),
                        ]),

                    // Remplacement (professeurs uniquement)
                    Grid::make(2)
                        ->visible(fn ($get) => filled($get('employee_id')) && Employee::find($get('employee_id'))?->isProfesseur())
                        ->schema([
                            Select::make('remplacant_id')
                                ->label('Professeur remplaçant')
                                ->relationship(
                                    'remplacant',
                                    'first_name',
                                    fn ($query, $get) => $query
                                        ->with('profession')
                                        ->whereHas('profession', fn ($q) => $q->whereIn('name', ['Enseignant', 'Enseignante', 'Professeur']))
                                        ->when($get('employee_id'), fn ($q, $id) => $q->where('id', '!=', $id))
                                )
                                ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name . ($record->profession ? ' — ' . $record->profession->name : ''))
                                ->searchable()
                                ->preload()
                                ->placeholder('Choisir un remplaçant…')
                                ->nullable(),

                            Select::make('type_cours')
                                ->label('Type de cours')
                                ->options(['exercice' => 'Exercice', 'lecon' => 'Leçon', 'activite' => 'Activité'])
                                ->placeholder('Sélectionner…')
                                ->nullable()
                                ->live(),
                        ]),

                    TextInput::make('nb_pages')
                        ->label('Nombre de pages')
                        ->numeric()->minValue(1)->nullable()
                        ->visible(fn ($get) => $get('type_cours') === 'exercice'),

                    TextInput::make('intitule_lecon')
                        ->label('Intitulé de la leçon')
                        ->maxLength(200)->nullable()
                        ->visible(fn ($get) => $get('type_cours') === 'lecon'),

                    TextInput::make('intitule_activite')
                        ->label('Intitulé de l\'activité')
                        ->maxLength(200)->nullable()
                        ->visible(fn ($get) => $get('type_cours') === 'activite'),

                    Grid::make(1)
                        ->hidden($isEmployee)
                        ->schema([
                            Textarea::make('actions_taken')
                                ->label('Mesures prises')
                                ->rows(2)->nullable(),

                            Textarea::make('rh_notes')
                                ->label('Notes RH')
                                ->rows(2)->nullable(),
                        ]),

                    FileUpload::make('justificatif')
                        ->label('Justificatif')
                        ->disk('public')
                        ->directory('leaves/justificatifs')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->maxSize(5120)
                        ->nullable()
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->helperText('PDF ou image — max 5 Mo.')
                        ->columnSpanFull(),
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
                    ->color(fn ($state) => $state === 'conge' ? 'info' : 'warning')
                    ->formatStateUsing(fn ($state) => $state === 'conge' ? 'Congé' : 'Absence'),
                TextColumn::make('leaveType.name')
                    ->label('Type de congé')
                    ->default('—')
                    ->sortable(),
                TextColumn::make('start_date')->label('Début')->date('d/m/Y')->sortable(),
                TextColumn::make('end_date')->label('Fin')->date('d/m/Y')->sortable(),
                TextColumn::make('duration_days')->label('Durée')->suffix(' j'),
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
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Supprimé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categorie')->label('Catégorie')->options(['conge' => 'Congé', 'absence' => 'Absence']),
                SelectFilter::make('status')->label('Statut')->options([
                    'en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé',
                ]),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('voir_justificatif')
                        ->label('Voir justificatif')
                        ->icon('heroicon-o-paper-clip')
                        ->color('info')
                        ->visible(fn (Leave $record) => filled($record->justificatif))
                        ->url(fn (Leave $record) => asset('storage/' . $record->justificatif))
                        ->openUrlInNewTab(),

                    Action::make('approve')
                        ->label('Approuver')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Leave $record) => $record->status === 'en_attente' && ! auth()->user()?->isBasicRole())
                        ->requiresConfirmation()
                        ->action(fn (Leave $record) => $record->update([
                            'status' => 'approuvé', 'approved_by' => auth()->id(), 'approved_at' => now(),
                        ])),

                    Action::make('reject')
                        ->label('Refuser')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Leave $record) => $record->status === 'en_attente' && ! auth()->user()?->isBasicRole())
                        ->requiresConfirmation()
                        ->action(fn (Leave $record) => $record->update([
                            'status' => 'refusé', 'approved_by' => auth()->id(), 'approved_at' => now(),
                        ])),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => ! auth()->user()?->isBasicRole()),
                ]),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit'   => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
