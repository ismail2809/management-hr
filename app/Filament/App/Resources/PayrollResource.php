<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PayrollResource\Pages;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollCalculator;
use Filament\Schemas\Components\Grid;
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
    protected static ?string $modelLabel = 'Fiche de paie';
    protected static \UnitEnum|string|null $navigationGroup = 'Paie';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Employé & Période')->schema([
                Grid::make(2)->schema([
                    Select::make('employee_id')
                        ->label('Employé')
                        ->relationship('employee', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                        ->searchable()
                        ->default(fn () => auth()->user()?->employee_id)
                        ->required(),

                    Select::make('status')
                        ->label('Statut')
                        ->options(['brouillon' => 'Brouillon', 'validé' => 'Validé', 'payé' => 'Payé'])
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
                        ->required(),

                    TextInput::make('year')
                        ->label('Année')
                        ->numeric()
                        ->default(now()->year)
                        ->required(),

                    TextInput::make('salaire_brut')
                        ->label('Salaire brut')
                        ->numeric()
                        ->required()
                        ->suffix('MAD'),
                ]),
            ]),

            Section::make('Heures supplémentaires & Prorata')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('overtime_hours')
                            ->label('Heures supplémentaires')
                            ->numeric()
                            ->default(0)
                            ->suffix('h'),
                        TextInput::make('overtime_amount')
                            ->label('Montant heures sup.')
                            ->numeric()
                            ->readOnly()
                            ->suffix('MAD'),
                        Toggle::make('is_prorata')
                            ->label('Mois incomplet (prorata)')
                            ->inline(false)
                            ->reactive(),
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

            Section::make('Résultat du calcul')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('total_cnss_employee')->label('CNSS salarié')->numeric()->readOnly()->suffix('MAD'),
                        TextInput::make('amo_employee')->label('AMO salarié')->numeric()->readOnly()->suffix('MAD'),
                        TextInput::make('ir')->label('IR')->numeric()->readOnly()->suffix('MAD'),
                    ]),
                    Grid::make(3)->schema([
                        TextInput::make('total_cnss_employer')->label('CNSS patronal')->numeric()->readOnly()->suffix('MAD'),
                        TextInput::make('amo_employer')->label('AMO patronal')->numeric()->readOnly()->suffix('MAD'),
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
                            Grid::make(4)->schema([
                                Select::make('type')
                                    ->label('Type')
                                    ->options(['prime' => 'Prime', 'retenue' => 'Retenue', 'avantage' => 'Avantage'])
                                    ->required(),
                                TextInput::make('label')->label('Libellé')->required(),
                                TextInput::make('amount')->label('Montant (MAD)')->numeric()->required(),
                                Toggle::make('taxable')->label('Imposable')->default(true)->inline(false),
                            ]),
                        ])
                        ->addActionLabel('Ajouter une composante')
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
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'brouillon' => 'gray',
                        'validé'    => 'warning',
                        'payé'      => 'success',
                        default     => 'gray',
                    }),
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
                Action::make('recalculate')
                    ->label('Recalculer')
                    ->icon('heroicon-o-calculator')
                    ->color('info')
                    ->visible(fn (Payroll $record) => $record->status === 'brouillon')
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
                    ->visible(fn (Payroll $record) => $record->status === 'brouillon')
                    ->requiresConfirmation()
                    ->action(fn (Payroll $record) => $record->update(['status' => 'validé'])),

                Action::make('mark_paid')
                    ->label('Marquer payé')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Payroll $record) => $record->status === 'validé')
                    ->requiresConfirmation()
                    ->action(fn (Payroll $record) => $record->update(['status' => 'payé'])),

                ActionGroup::make([
                    Action::make('download_pdf')
                        ->label('Bulletin PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->url(fn (Payroll $record) => route('payrolls.pdf', $record))
                        ->openUrlInNewTab(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
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
