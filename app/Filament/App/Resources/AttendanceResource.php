<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
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
    protected static \UnitEnum|string|null $navigationGroup = 'Présence';
    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Select::make('employee_id')
                    ->label('Employé')
                    ->relationship('employee', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                    ->searchable()
                    ->default(fn () => auth()->user()?->employee_id)
                    ->required(),

                DatePicker::make('date')
                    ->label('Date')
                    ->required(),
            ]),

            Grid::make(2)->schema([
                TimePicker::make('check_in')
                    ->label('Heure d\'arrivée')
                    ->seconds(false)
                    ->nullable(),

                TimePicker::make('check_out')
                    ->label('Heure de départ')
                    ->seconds(false)
                    ->nullable(),
            ]),

            Grid::make(2)->schema([
                TextInput::make('worked_hours')
                    ->label('Heures travaillées')
                    ->numeric()
                    ->default(0)
                    ->readOnly()
                    ->helperText('Calculé automatiquement depuis check_in/check_out'),

                TextInput::make('overtime_hours')
                    ->label('Heures supplémentaires')
                    ->numeric()
                    ->default(0)
                    ->readOnly()
                    ->helperText('Au-delà de 8h/jour'),
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
                    ->sortable(),
                TextColumn::make('date')->label('Date')->date('d/m/Y')->sortable(),
                TextColumn::make('check_in')->label('Arrivée')->time('H:i')->default('—'),
                TextColumn::make('check_out')->label('Départ')->time('H:i')->default('—'),
                TextColumn::make('worked_hours')->label('Heures trav.')->suffix('h')->sortable(),
                TextColumn::make('overtime_hours')->label('H. sup.')->suffix('h')->sortable(),
            ])
            ->filters([
                SelectFilter::make('employee_id')
                    ->label('Employé')
                    ->relationship('employee', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name),

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
