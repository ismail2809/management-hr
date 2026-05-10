<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PayrollResource\Pages;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Builder;
use App\Services\PayrollCalculator;
use App\Models\Leave;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Fiches de paie';

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->hasRole('employee') ? 'Mes bulletins de paie' : 'Fiches de paie';
    }

    public static function getModelLabel(): string
    {
        return auth()->user()?->hasRole('employee') ? 'Bulletin de paie' : 'Fiche de paie';
    }
    protected static ?string $modelLabel = 'Fiche de paie';
    protected static \UnitEnum|string|null $navigationGroup = 'Paie';
    protected static ?int $navigationSort = 5;

    public static function canCreate(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
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
        $locked = fn ($get, $record) => $record?->isLocked() ?? false;

        return $schema->columns(1)->components([
            Section::make('Employé & Période')->schema([
                Grid::make(2)->schema([
                    Select::make('employee_id')
                        ->label('Employé')
                        ->relationship('employee', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                        ->searchable()
                        ->default(fn () => auth()->user()?->employee_id)
                        ->disabled(fn ($record) => $record?->isLocked() ?? false)
                        ->required(),

                    Select::make('status')
                        ->label('Statut')
                        ->options(['brouillon' => 'Brouillon', 'validé' => 'Validé', 'payé' => 'Payé'])
                        ->disabled(fn ($record) => $record?->isLocked() ?? false)
                        ->required(),
                ]),

                Grid::make(3)->schema([
                    Select::make('month')
                        ->label('Mois')
                        ->options([
                            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
                            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
                            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
                        ])
                        ->disabled(fn ($record) => $record?->isLocked() ?? false)
                        ->required(),

                    TextInput::make('year')
                        ->label('Année')
                        ->numeric()
                        ->default(now()->year)
                        ->disabled(fn ($record) => $record?->isLocked() ?? false)
                        ->required(),

                    TextInput::make('salaire_brut')
                        ->label('Salaire brut')
                        ->numeric()
                        ->required()
                        ->disabled(fn ($record) => $record?->isLocked() ?? false)
                        ->suffix('MAD'),
                ]),
            ]),

            Section::make('Heures supplémentaires & Prorata')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('overtime_hours')
                            ->label('Heures sup. jour (+25%)')
                            ->numeric()
                            ->default(0)
                            ->disabled(fn ($record) => $record?->isLocked() ?? false)
                            ->suffix('h'),
                        TextInput::make('overtime_hours_night')
                            ->label('Heures sup. nuit (+50%)')
                            ->numeric()
                            ->default(0)
                            ->disabled(fn ($record) => $record?->isLocked() ?? false)
                            ->suffix('h'),
                        Toggle::make('is_prorata')
                            ->label('Mois incomplet (prorata)')
                            ->inline(false)
                            ->disabled(fn ($record) => $record?->isLocked() ?? false)
                            ->reactive(),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('overtime_amount')
                            ->label('Montant HS jour')
                            ->numeric()
                            ->readOnly()
                            ->suffix('MAD'),
                        TextInput::make('overtime_amount_night')
                            ->label('Montant HS nuit')
                            ->numeric()
                            ->readOnly()
                            ->suffix('MAD'),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('worked_days')
                            ->label('Jours travaillés')
                            ->numeric()
                            ->visible(fn ($get) => $get('is_prorata')),
                        TextInput::make('total_working_days')
                            ->label('Jours ouvrables du mois')
                            ->numeric()
                            ->readOnly()
                            ->visible(fn ($get) => $get('is_prorata')),
                    ]),
                ])
                ->collapsible()
                ->collapsed(),

            Section::make('Absences sans solde')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('absence_days')
                            ->label('Jours d\'absence sans solde')
                            ->numeric()
                            ->readOnly()
                            ->suffix('j')
                            ->helperText('Calculé automatiquement depuis les congés "Sans solde" approuvés'),
                        TextInput::make('absence_deduction')
                            ->label('Déduction absence')
                            ->numeric()
                            ->readOnly()
                            ->suffix('MAD'),
                    ]),
                    Placeholder::make('pending_leaves_warning')
                        ->label('')
                        ->content(function ($get) {
                            $employeeId = $get('employee_id');
                            $month      = $get('month');
                            $year       = $get('year');
                            if (! $employeeId || ! $month || ! $year) {
                                return null;
                            }
                            $pending = Leave::withoutGlobalScopes()
                                ->where('employee_id', $employeeId)
                                ->where('status', 'en_attente')
                                ->whereMonth('start_date', $month)
                                ->whereYear('start_date', $year)
                                ->count();
                            if ($pending > 0) {
                                return "⚠️ Attention : {$pending} demande(s) de congé en attente de validation pour cette période.";
                            }
                            return null;
                        })
                        ->reactive(),
                ])
                ->collapsible()
                ->collapsed(),

            Section::make('Prime ancienneté')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('anciennete_years')
                            ->label('Ancienneté (ans)')
                            ->numeric()
                            ->readOnly()
                            ->suffix('ans'),
                        TextInput::make('anciennete_rate')
                            ->label('Taux ancienneté')
                            ->numeric()
                            ->readOnly()
                            ->suffix('%'),
                        TextInput::make('anciennete_amount')
                            ->label('Montant prime ancienneté')
                            ->numeric()
                            ->readOnly()
                            ->suffix('MAD'),
                    ]),
                ])
                ->collapsible()
                ->collapsed(),

            Section::make('Résultat du calcul')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('total_cnss_employee')
                            ->label('CNSS salarié')
                            ->numeric()->readOnly()->suffix('MAD')
                            ->helperText(fn ($record) => $record?->cnss_employee_rate ? "Taux : {$record->cnss_employee_rate}%" : null),
                        TextInput::make('amo_employee')
                            ->label('AMO salarié')
                            ->numeric()->readOnly()->suffix('MAD')
                            ->helperText(fn ($record) => $record?->amo_employee_rate ? "Taux : {$record->amo_employee_rate}%" : null),
                        TextInput::make('ir')
                            ->label('IR')
                            ->numeric()->readOnly()->suffix('MAD')
                            ->helperText(fn ($record) => $record?->ir_rate_applied ? "Tranche : {$record->ir_rate_applied}%" : null),
                    ]),
                    Grid::make(3)->schema([
                        TextInput::make('total_cnss_employer')
                            ->label('CNSS patronal')
                            ->numeric()->readOnly()->suffix('MAD')
                            ->helperText(fn ($record) => $record?->cnss_employer_rate ? "Taux : {$record->cnss_employer_rate}%" : null),
                        TextInput::make('amo_employer')
                            ->label('AMO patronal')
                            ->numeric()->readOnly()->suffix('MAD')
                            ->helperText(fn ($record) => $record?->amo_employer_rate ? "Taux : {$record->amo_employer_rate}%" : null),
                        TextInput::make('salaire_net')->label('Salaire net')->numeric()->readOnly()->suffix('MAD'),
                    ]),
                ])
                ->collapsible(),

            Section::make('Composantes (primes / retenues / avantages)')
                ->schema([
                    Repeater::make('components')
                        ->label('')
                        ->relationship()
                        ->schema([
                            Grid::make(3)->schema([
                                Select::make('type')
                                    ->label('Type')
                                    ->options(\App\Models\PayrollComponent::TYPES)
                                    ->required()
                                    ->helperText(fn ($state) => match ($state) {
                                        'prime_imposable'     => 'Entre dans la base CNSS et IR',
                                        'prime_non_imposable' => 'Exclue de CNSS et IR',
                                        'avantage_imposable'  => 'Entre dans la base IR',
                                        'retenue'             => 'Déduite du salaire net',
                                        default               => null,
                                    }),
                                TextInput::make('label')->label('Libellé')->required(),
                                TextInput::make('amount')->label('Montant (MAD)')->numeric()->required(),
                            ]),
                        ])
                        ->addActionLabel('Ajouter une composante')
                        ->disabled(fn ($record) => $record?->isLocked() ?? false)
                        ->collapsible(),
                ])
                ->collapsible()
                ->collapsed(),
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
                TextColumn::make('periode_label')
                    ->label('Période')
                    ->sortable('year'),
                TextColumn::make('salaire_brut')
                    ->label('Brut')
                    ->money('MAD')
                    ->sortable(),
                TextColumn::make('total_cnss_employee')
                    ->label('CNSS')
                    ->money('MAD')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ir')
                    ->label('IR')
                    ->money('MAD')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('salaire_net')
                    ->label('Net')
                    ->money('MAD')
                    ->sortable()
                    ->weight('semibold')
                    ->color('success'),
                TextColumn::make('anciennete_rate')
                    ->label('Anc.')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state . '%' : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'brouillon' => 'gray',
                        'validé'    => 'warning',
                        'payé'      => 'success',
                        default     => 'gray',
                    })
                    ->description(fn (Payroll $record) => $record->isLocked() ? '🔒 Verrouillé' : null),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(['brouillon' => 'Brouillon', 'validé' => 'Validé', 'payé' => 'Payé']),
                SelectFilter::make('month')
                    ->label('Mois')
                    ->options([
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
                        4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                        7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
                        10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
                    ]),
            ])
            ->actions([
                Action::make('download_pdf_employee')
                    ->label('Voir Bulletin PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->visible(fn (Payroll $record) => auth()->user()?->hasRole('employee') && in_array($record->status, ['validé', 'payé']))
                    ->url(fn (Payroll $record) => route('payrolls.pdf', $record))
                    ->openUrlInNewTab(true),

                Action::make('recalculate')
                    ->label('Recalculer')
                    ->icon('heroicon-o-calculator')
                    ->color('info')
                    ->visible(fn (Payroll $record) => $record->status === 'brouillon' && ! auth()->user()?->hasRole('employee'))
                    ->requiresConfirmation()
                    ->modalHeading('Recalculer la fiche de paie')
                    ->modalDescription('Les montants CNSS, AMO et IR seront recalculés selon les taux en vigueur.')
                    ->action(function (Payroll $record) {
                        $components = $record->components->map(fn ($c) => [
                            'type'    => $c->type,
                            'label'   => $c->label,
                            'amount'  => (float) $c->amount,
                            'taxable' => (bool) $c->taxable,
                        ])->toArray();

                        (new PayrollCalculator())->calculate(
                            $record->employee,
                            $record->month,
                            $record->year,
                            (float) $record->salaire_brut,
                            $components
                        );
                    }),

                Action::make('validate')
                    ->label('Valider')
                    ->icon('heroicon-o-check-badge')
                    ->color('warning')
                    ->visible(fn (Payroll $record) => $record->status === 'brouillon' && ! auth()->user()?->hasRole('employee'))
                    ->requiresConfirmation()
                    ->action(fn (Payroll $record) => $record->update(['status' => 'validé'])),

                Action::make('mark_paid')
                    ->label('Marquer payé')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Payroll $record) => $record->status === 'validé' && ! auth()->user()?->hasRole('employee'))
                    ->requiresConfirmation()
                    ->action(fn (Payroll $record) => $record->update(['status' => 'payé'])),

                ActionGroup::make([
                    Action::make('download_pdf')
                        ->label('Bulletin PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->url(fn (Payroll $record) => route('payrolls.pdf', $record))
                        ->openUrlInNewTab(),
                ])->icon('heroicon-m-ellipsis-horizontal')
                  ->visible(fn () => ! auth()->user()?->hasRole('employee')),
            ])
            ->bulkActions([
                BulkAction::make('bulk_pdf')
                    ->label('Télécharger bulletins (ZIP)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function (Collection $records, $livewire) {
                        $ids = $records->pluck('id')->toArray();
                        $livewire->redirect(route('payrolls.bulk-pdf', ['ids' => $ids]));
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('year', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'edit'   => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }
}
