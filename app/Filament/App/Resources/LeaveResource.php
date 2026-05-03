<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\LeaveResource\Pages;
use App\Models\Employee;
use App\Models\Leave;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Demandes de congé';
    protected static ?string $modelLabel = 'Congé';
    protected static \UnitEnum|string|null $navigationGroup = 'Congés & Présence';
    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Demandeur & Type')->schema([
                Grid::make(2)->schema([
                    Select::make('employee_id')
                        ->label('Employé')
                        ->relationship('employee', 'first_name')
                        ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                        ->searchable()
                        ->default(fn () => auth()->user()?->employee_id)
                        ->required(),

                    Select::make('leave_type_id')
                        ->label('Type de congé')
                        ->relationship('leaveType', 'name')
                        ->required(),
                ]),
            ]),

            Section::make('Période')->schema([
                Grid::make(2)->schema([
                    DatePicker::make('start_date')->label('Date de début')->required(),
                    DatePicker::make('end_date')->label('Date de fin')->required(),
                ]),

                Textarea::make('reason')
                    ->label('Motif (optionnel)')
                    ->rows(3)
                    ->nullable(),
            ]),

            Section::make('Décision')->schema([
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
                TextColumn::make('leaveType.name')
                    ->label('Type')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('start_date')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('duration_days')
                    ->label('Durée')
                    ->suffix(' j')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'en_attente' => 'warning',
                        'approuvé'   => 'success',
                        'refusé'     => 'danger',
                        default      => 'gray',
                    }),
                TextColumn::make('approver.name')
                    ->label('Approuvé par')
                    ->default('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'en_attente' => 'En attente',
                        'approuvé'   => 'Approuvé',
                        'refusé'     => 'Refusé',
                    ]),
                SelectFilter::make('leave_type_id')
                    ->label('Type de congé')
                    ->relationship('leaveType', 'name'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Leave $record) => $record->status === 'en_attente')
                    ->requiresConfirmation()
                    ->action(fn (Leave $record) => $record->update([
                        'status'      => 'approuvé',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ])),

                Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Leave $record) => $record->status === 'en_attente')
                    ->requiresConfirmation()
                    ->action(fn (Leave $record) => $record->update([
                        'status'      => 'refusé',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ])),
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
