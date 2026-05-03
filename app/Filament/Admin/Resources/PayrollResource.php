<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PayrollResource\Pages;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollCalculator;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Paie';
    protected static ?string $modelLabel = 'Fiche de paie';
    protected static \UnitEnum|string|null $navigationGroup = 'RH';
    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Entreprise & Employé')->schema([
                Grid::make(2)->schema([
                    Select::make('company_id')
                        ->label('Entreprise')
                        ->options(Company::pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('employee_id', null)),

                    Select::make('employee_id')
                        ->label('Employé')
                        ->options(fn ($get) => Employee::withoutGlobalScopes()
                            ->when($get('company_id'), fn ($q, $cid) => $q->where('company_id', $cid))
                            ->get()->pluck('full_name', 'id'))
                        ->searchable()
                        ->required(),
                ]),

                Grid::make(3)->schema([
                    Select::make('month')
                        ->label('Mois')
                        ->options([1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'])
                        ->required(),
                    TextInput::make('year')->label('Année')->numeric()->default(now()->year)->required(),
                    Select::make('status')->label('Statut')->options(['brouillon' => 'Brouillon', 'validé' => 'Validé', 'payé' => 'Payé'])->required(),
                ]),

                TextInput::make('salaire_brut')->label('Salaire brut')->numeric()->required()->suffix('MAD'),
            ]),

            Section::make('Résultat du calcul')->schema([
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
            ])->collapsible(),

            Section::make('Composantes')->schema([
                Repeater::make('components')
                    ->label('')
                    ->relationship()
                    ->schema([
                        Grid::make(4)->schema([
                            Select::make('type')->label('Type')->options(['prime' => 'Prime', 'retenue' => 'Retenue', 'avantage' => 'Avantage'])->required(),
                            TextInput::make('label')->label('Libellé')->required(),
                            TextInput::make('amount')->label('Montant')->numeric()->required()->suffix('MAD'),
                            Toggle::make('taxable')->label('Imposable')->default(true)->inline(false),
                        ]),
                    ])
                    ->addActionLabel('Ajouter une composante')
                    ->collapsible(),
            ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Entreprise')->sortable()->badge()->color('gray'),
                TextColumn::make('employee.full_name')->label('Employé')->searchable(['employees.first_name', 'employees.last_name'])->sortable()->weight('semibold'),
                TextColumn::make('periode_label')->label('Période'),
                TextColumn::make('salaire_brut')->label('Brut')->money('MAD')->sortable(),
                TextColumn::make('salaire_net')->label('Net')->money('MAD')->sortable()->color('success')->weight('semibold'),
                TextColumn::make('status')->label('Statut')->badge()
                    ->color(fn ($state) => match ($state) {
                        'brouillon' => 'gray', 'validé' => 'warning', 'payé' => 'success', default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Entreprise')->options(Company::pluck('name', 'id')),
                SelectFilter::make('status')->label('Statut')->options(['brouillon' => 'Brouillon', 'validé' => 'Validé', 'payé' => 'Payé']),
            ])
            ->actions([
                Action::make('recalculate')
                    ->label('Recalculer')
                    ->icon('heroicon-o-calculator')
                    ->color('info')
                    ->visible(fn (Payroll $record) => $record->status === 'brouillon')
                    ->requiresConfirmation()
                    ->action(function (Payroll $record) {
                        $components = $record->components->map(fn ($c) => ['type' => $c->type, 'label' => $c->label, 'amount' => (float) $c->amount, 'taxable' => (bool) $c->taxable])->toArray();
                        (new PayrollCalculator())->calculate($record->employee, $record->month, $record->year, (float) $record->salaire_brut, $components);
                    }),

                Action::make('validate')
                    ->label('Valider')
                    ->icon('heroicon-o-check-badge')
                    ->color('warning')
                    ->visible(fn (Payroll $record) => $record->status === 'brouillon')
                    ->requiresConfirmation()
                    ->action(fn (Payroll $record) => $record->update(['status' => 'validé'])),

                Action::make('mark_paid')
                    ->label('Payé')
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
                BulkAction::make('bulk_validate')
                    ->label('Valider la sélection')
                    ->icon('heroicon-o-check-badge')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'validé']))
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
