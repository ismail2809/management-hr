<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasCompanyField;
use App\Filament\App\Resources\LeaveResource\Pages;
use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Get;
use Filament\Forms\Components\Radio;
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
    protected static ?string $modelLabel = 'Demande';
    protected static \UnitEnum|string|null $navigationGroup = 'Congés & Présence';
    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->hasRole('employee') ? 'Mes absences / congés' : 'Absences / Congés';
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
        return ! auth()->user()?->hasRole('employee');
    }

    public static function canDeleteAny(): bool
    {
        return ! auth()->user()?->hasRole('employee');
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
            Section::make('Demandeur')->schema([
                static::companyField(),
                Grid::make(2)->schema([
                    Select::make('employee_id')
                        ->label('Employé')
                        ->relationship('employee', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                        ->searchable()
                        ->preload()
                        ->default(fn () => auth()->user()?->employee_id)
                        ->disabled($isEmployee)
                        ->dehydrated()
                        ->required()
                        ->live(),
                    Radio::make('categorie')
                        ->label('Type de demande')
                        ->options(['conge' => 'Congé', 'absence' => 'Absence'])
                        ->default('conge')
                        ->inline()
                        ->required()
                        ->live(),
                ]),
            ]),

            Section::make('Congé')
                ->visible(fn (Get $get) => $get('categorie') === 'conge')
                ->schema([
                    Select::make('leave_type_id')
                        ->label('Type de congé')
                        ->relationship('leaveType', 'name')
                        ->nullable(),
                ]),

            Section::make('Période')->schema([
                Grid::make(2)->schema([
                    DatePicker::make('start_date')->label('Date de début')->required(),
                    DatePicker::make('end_date')->label('Date de fin')->required(),
                ]),
                Textarea::make('reason')->label('Motif')->rows(2)->nullable(),
                FileUpload::make('justificatif')
                    ->label('Justificatif (optionnel)')
                    ->disk('public')
                    ->directory('leaves/justificatifs')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(5120)
                    ->nullable(),
            ]),

            Section::make('Informations professeur (remplacement)')
                ->icon('heroicon-o-academic-cap')
                ->visible(fn (Get $get) => filled($get('employee_id')) && Employee::find($get('employee_id'))?->isProfesseur())
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('remplacant_id')
                            ->label('Professeur remplaçant')
                            ->relationship('remplacant', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Select::make('type_cours')
                            ->label('Type de cours prévu')
                            ->options([
                                'exercice' => 'Exercice',
                                'lecon'    => 'Leçon',
                                'activite' => 'Activité',
                            ])
                            ->nullable()
                            ->live(),
                    ]),
                    TextInput::make('nb_pages')
                        ->label('Nombre de pages')
                        ->numeric()
                        ->nullable()
                        ->visible(fn (Get $get) => $get('type_cours') === 'exercice'),
                    TextInput::make('intitule_lecon')
                        ->label('Intitulé de la leçon')
                        ->maxLength(200)
                        ->nullable()
                        ->visible(fn (Get $get) => $get('type_cours') === 'lecon'),
                    TextInput::make('intitule_activite')
                        ->label('Intitulé de l\'activité')
                        ->maxLength(200)
                        ->nullable()
                        ->visible(fn (Get $get) => $get('type_cours') === 'activite'),
                ]),

            Section::make('Décision')
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
