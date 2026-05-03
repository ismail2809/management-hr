<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Présences';
    protected static ?string $modelLabel = 'Pointage';
    protected static \UnitEnum|string|null $navigationGroup = 'RH';
    protected static ?int $navigationSort = 10;

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

                DatePicker::make('date')->label('Date')->required(),
            ]),

            Section::make('Horaires')->schema([
                Grid::make(2)->schema([
                    TimePicker::make('check_in')->label("Heure d'arrivée")->seconds(false)->nullable(),
                    TimePicker::make('check_out')->label('Heure de départ')->seconds(false)->nullable(),
                ]),
            ]),

            Section::make('Totaux calculés')->schema([
                Grid::make(2)->schema([
                    TextInput::make('worked_hours')->label('Heures travaillées')->numeric()->default(0)->readOnly()->suffix('h')->helperText('Calculé automatiquement'),
                    TextInput::make('overtime_hours')->label('Heures supplémentaires')->numeric()->default(0)->readOnly()->suffix('h')->helperText('Au-delà de 8h/jour'),
                ]),
            ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Entreprise')->sortable()->badge()->color('gray'),
                TextColumn::make('employee.full_name')->label('Employé')->searchable(['employees.first_name', 'employees.last_name'])->sortable()->weight('semibold'),
                TextColumn::make('date')->label('Date')->date('d/m/Y')->sortable(),
                TextColumn::make('check_in')->label('Arrivée')->time('H:i')->default('—'),
                TextColumn::make('check_out')->label('Départ')->time('H:i')->default('—'),
                TextColumn::make('worked_hours')->label('Heures trav.')->suffix('h')->sortable()
                    ->color(fn ($state) => $state >= 8 ? 'success' : ($state > 0 ? 'warning' : 'danger')),
                TextColumn::make('overtime_hours')->label('H. sup.')->suffix('h')->sortable()->color('warning')->default('0'),
            ])
            ->filters([
                SelectFilter::make('company_id')->label('Entreprise')->options(Company::pluck('name', 'id')),

                Filter::make('date')
                    ->form([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'],  fn ($q, $v) => $q->whereDate('date', '>=', $v))
                            ->when($data['until'], fn ($q, $v) => $q->whereDate('date', '<=', $v));
                    }),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit'   => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
