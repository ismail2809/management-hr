<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\LeaveResource\Pages;
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
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
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
    protected static ?string $modelLabel = 'Demande d\'absence';
    protected static ?string $pluralModelLabel = 'Demandes d\'absences';
    protected static \UnitEnum|string|null $navigationGroup = 'Congés & Présence';
    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->hasRole('employee') ? 'Demandes d\'absences' : 'Absences / Congés';
    }

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()?->hasRole('employee') ? 'Mes demandes' : 'Congés & Présence';
    }

    public static function getNavigationSort(): ?int
    {
        return auth()->user()?->hasRole('employee') ? 1 : 7;
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

            // ── Section 1 : Demandeur ─────────────────────────────────────────
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
                                ->live()
                                ->columnSpanFull(),
                        ]),
                ]),

            // ── Section 2 : Type de demande ──────────────────────────────────
            Section::make('Type de demande')
                ->description('Choisissez s\'il s\'agit d\'un congé planifié ou d\'une absence.')
                ->icon('heroicon-o-tag')
                ->columns(2)
                ->schema([
                    ToggleButtons::make('categorie')
                        ->label('Catégorie')
                        ->options(['conge' => 'Congé', 'absence' => 'Absence'])
                        ->icons(['conge' => 'heroicon-o-sun', 'absence' => 'heroicon-o-exclamation-triangle'])
                        ->colors(['conge' => 'info', 'absence' => 'warning'])
                        ->default('conge')
                        ->grouped()
                        ->required()
                        ->live(),

                    Select::make('leave_type_id')
                        ->label('Type de congé')
                        ->options(fn () => LeaveType::orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->placeholder('Sélectionner un type…')
                        ->nullable()
                        ->visible(fn ($get) => $get('categorie') === 'conge')
                        ->helperText('Laissez vide si le type n\'est pas applicable.'),
                ]),

            // ── Section 3 : Période ───────────────────────────────────────────
            Section::make('Période')
                ->description('Définissez les dates et ajoutez un motif ou un justificatif.')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Grid::make(3)->schema([
                        DatePicker::make('start_date')
                            ->label('Date de début')
                            ->required()
                            ->live()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        DatePicker::make('end_date')
                            ->label('Date de fin')
                            ->required()
                            ->live()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->minDate(fn ($get) => $get('start_date'))
                            ->columnSpan(1),

                        Placeholder::make('duree_calculee')
                            ->label('Durée estimée')
                            ->content(function ($get): string {
                                $start = $get('start_date');
                                $end   = $get('end_date');
                                if (! $start || ! $end) {
                                    return '—';
                                }
                                try {
                                    $days = Carbon::parse($start)->diffInWeekdays(Carbon::parse($end)) + 1;
                                    return $days . ' jour' . ($days > 1 ? 's' : '') . ' ouvrable' . ($days > 1 ? 's' : '');
                                } catch (\Exception) {
                                    return '—';
                                }
                            })
                            ->columnSpan(1),
                    ]),

                    Textarea::make('reason')
                        ->label('Motif')
                        ->placeholder('Décrivez brièvement la raison de cette demande…')
                        ->rows(3)
                        ->nullable(),

                    FileUpload::make('justificatif')
                        ->label('Justificatif')
                        ->disk('public')
                        ->directory('leaves/justificatifs')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->maxSize(5120)
                        ->nullable()
                        ->helperText('PDF ou image — max 5 Mo. Optionnel.'),
                ]),

            // ── Section 4 : Remplacement (professeurs) ────────────────────────
            Section::make('Remplacement')
                ->description('Désignez un remplaçant et précisez le contenu prévu.')
                ->icon('heroicon-o-academic-cap')
                ->visible(fn ($get) => filled($get('employee_id')) && Employee::find($get('employee_id'))?->isProfesseur())
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('remplacant_id')
                            ->label('Professeur remplaçant')
                            ->relationship(
                                'remplacant',
                                'first_name',
                                fn ($query, $get) => $query->when(
                                    $get('employee_id'),
                                    fn ($q, $id) => $q->where('id', '!=', $id)
                                )
                            )
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                            ->searchable()
                            ->preload()
                            ->placeholder('Choisir un remplaçant…')
                            ->nullable(),

                        Select::make('type_cours')
                            ->label('Type de cours prévu')
                            ->options([
                                'exercice' => 'Exercice',
                                'lecon'    => 'Leçon',
                                'activite' => 'Activité',
                            ])
                            ->placeholder('Sélectionner…')
                            ->nullable()
                            ->live(),
                    ]),

                    TextInput::make('nb_pages')
                        ->label('Nombre de pages')
                        ->numeric()
                        ->minValue(1)
                        ->nullable()
                        ->visible(fn ($get) => $get('type_cours') === 'exercice'),

                    TextInput::make('intitule_lecon')
                        ->label('Intitulé de la leçon')
                        ->maxLength(200)
                        ->nullable()
                        ->visible(fn ($get) => $get('type_cours') === 'lecon'),

                    TextInput::make('intitule_activite')
                        ->label('Intitulé de l\'activité')
                        ->maxLength(200)
                        ->nullable()
                        ->visible(fn ($get) => $get('type_cours') === 'activite'),
                ]),

            // ── Section 5 : Décision ──────────────────────────────────────────
            Section::make('Décision')
                ->description('Définissez le statut de cette demande.')
                ->icon('heroicon-o-clipboard-document-check')
                ->hidden($isEmployee)
                ->schema([
                    ToggleButtons::make('status')
                        ->label('Statut')
                        ->options([
                            'en_attente' => 'En attente',
                            'approuvé'   => 'Approuvé',
                            'refusé'     => 'Refusé',
                        ])
                        ->icons([
                            'en_attente' => 'heroicon-o-clock',
                            'approuvé'   => 'heroicon-o-check-circle',
                            'refusé'     => 'heroicon-o-x-circle',
                        ])
                        ->colors([
                            'en_attente' => 'warning',
                            'approuvé'   => 'success',
                            'refusé'     => 'danger',
                        ])
                        ->default('en_attente')
                        ->grouped()
                        ->required(),
                ]),

            // ── Section 6 : Suivi RH ──────────────────────────────────────────
            Section::make('Suivi RH')
                ->description('Informations internes à l\'équipe RH.')
                ->icon('heroicon-o-shield-check')
                ->hidden($isEmployee)
                ->schema([
                    Grid::make(2)->schema([
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

                    Grid::make(2)->schema([
                        Textarea::make('actions_taken')
                            ->label('Mesures prises')
                            ->rows(3)
                            ->nullable(),

                        Textarea::make('rh_notes')
                            ->label('Notes RH')
                            ->rows(3)
                            ->nullable(),
                    ]),
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
            ])
            ->filters([
                SelectFilter::make('categorie')->label('Catégorie')->options(['conge' => 'Congé', 'absence' => 'Absence']),
                SelectFilter::make('status')->label('Statut')->options([
                    'en_attente' => 'En attente', 'approuvé' => 'Approuvé', 'refusé' => 'Refusé',
                ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Leave $record) => $record->status === 'en_attente' && ! auth()->user()?->hasRole('employee'))
                    ->requiresConfirmation()
                    ->action(fn (Leave $record) => $record->update([
                        'status' => 'approuvé', 'approved_by' => auth()->id(), 'approved_at' => now(),
                    ])),
                Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Leave $record) => $record->status === 'en_attente' && ! auth()->user()?->hasRole('employee'))
                    ->requiresConfirmation()
                    ->action(fn (Leave $record) => $record->update([
                        'status' => 'refusé', 'approved_by' => auth()->id(), 'approved_at' => now(),
                    ])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => ! auth()->user()?->hasRole('employee')),
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
