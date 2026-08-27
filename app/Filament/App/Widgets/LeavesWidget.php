<?php

namespace App\Filament\App\Widgets;

use App\Models\Leave;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LeavesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Absences & Congés';

    public static function canView(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Leave::query()
                    ->with('employee')
                    ->orderByDesc('start_date')
            )
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->searchable(['employees.first_name', 'employees.last_name'])
                    ->weight('semibold'),
                TextColumn::make('categorie')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => $state === 'conge' ? 'info' : 'warning')
                    ->formatStateUsing(fn ($state) => $state === 'conge' ? 'Congé' : 'Absence'),
                TextColumn::make('start_date')
                    ->label('Début')
                    ->date('d/m/Y'),
                TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y'),
                TextColumn::make('duration_days')
                    ->label('Durée')
                    ->suffix(' j'),
                TextColumn::make('reason')
                    ->label('Motif')
                    ->limit(40)
                    ->default('—'),
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
                SelectFilter::make('periode')
                    ->label('Période')
                    ->options([
                        'today'  => "Aujourd'hui",
                        'week'   => 'Cette semaine',
                        'recent' => 'Demandes récentes (7j)',
                    ])
                    ->default('today')
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value'] ?? 'today') {
                            'today' => $query
                                ->where('status', 'approuvé')
                                ->whereDate('start_date', '<=', today())
                                ->whereDate('end_date', '>=', today()),
                            'week' => $query
                                ->where('status', 'approuvé')
                                ->whereDate('start_date', '<=', now()->endOfWeek())
                                ->whereDate('end_date', '>=', now()->startOfWeek()),
                            'recent' => $query
                                ->where('status', 'en_attente')
                                ->where('created_at', '>=', now()->subDays(7)),
                            default => $query
                                ->whereDate('start_date', '<=', today())
                                ->whereDate('end_date', '>=', today()),
                        };
                    }),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->paginated(10);
    }
}
