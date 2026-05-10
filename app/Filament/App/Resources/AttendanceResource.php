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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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
    protected static \UnitEnum|string|null $navigationGroup = 'Congés & Présence';
    protected static ?int $navigationSort = 8;

    public static function canViewAny(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

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
        return $schema->columns(1)->components([
            Section::make('Employé & Date')->schema([
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
            ]),

            Section::make('Horaires')->schema([
                Grid::make(2)->schema([
                    TimePicker::make('check_in')
                        ->label("Heure d'arrivée")
                        ->seconds(false)
                        ->nullable(),

                    TimePicker::make('check_out')
                        ->label('Heure de départ')
                        ->seconds(false)
                        ->nullable(),
                ]),
            ]),

            Section::make('Totaux calculés')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('worked_hours')
                            ->label('Heures travaillées')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->suffix('h')
                            ->helperText('Calculé automatiquement'),

                        TextInput::make('overtime_hours')
                            ->label('Heures supplémentaires')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->suffix('h')
                            ->helperText('Au-delà de 8h/jour'),
                    ]),
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
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('check_in')
                    ->label('Arrivée')
                    ->time('H:i')
                    ->default('—'),
                TextColumn::make('check_out')
                    ->label('Départ')
                    ->time('H:i')
                    ->default('—'),
                TextColumn::make('worked_hours')
                    ->label('Heures trav.')
                    ->suffix('h')
                    ->sortable()
                    ->color(fn ($state) => $state >= 8 ? 'success' : ($state > 0 ? 'warning' : 'danger')),
                TextColumn::make('overtime_hours')
                    ->label('H. sup.')
                    ->suffix('h')
                    ->sortable()
                    ->color('warning')
                    ->default('0'),
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
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
