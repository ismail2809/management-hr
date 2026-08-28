<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AuditLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class AuditLogResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Journal d\'audit';
    protected static ?string $modelLabel = 'Entrée';
    protected static \UnitEnum|string|null $navigationGroup = 'Journal d\'audit';
    protected static ?int $navigationSort = 1;

    // Lecture seule — pas de create/edit
    public static function canCreate(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()
                    ->whereIn('log_name', ['payroll', 'employee', 'declaration', 'cnss_rate'])
                    ->with(['causer.employee'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('log_name')
                    ->label('Module')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'payroll'     => 'success',
                        'employee'    => 'primary',
                        'declaration' => 'warning',
                        'cnss_rate'   => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'payroll'     => 'Paie',
                        'employee'    => 'Employé',
                        'declaration' => 'Déclaration',
                        'cnss_rate'   => 'Taux CNSS',
                        default       => $state,
                    }),
                TextColumn::make('event')
                    ->label('Action')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'created' => 'Créé',
                        'updated' => 'Modifié',
                        'deleted' => 'Supprimé',
                        default   => $state,
                    }),
                TextColumn::make('causer_employee')
                    ->label('Employé associé')
                    ->state(function ($record) {
                        $employee = $record->causer?->employee;
                        if ($employee) {
                            return "{$employee->first_name} {$employee->last_name}";
                        }
                        return $record->causer?->name ?? '—';
                    })
                    ->default('—'),
                TextColumn::make('subject_type')
                    ->label('Objet')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject_id')
                    ->label('ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('properties')
                    ->label('Champs modifiés')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return '—';
                        $props  = is_array($state) ? $state : json_decode($state, true);
                        $keys   = array_keys($props['attributes'] ?? $props ?? []);
                        $labels = config('audit_labels.fields', []);
                        $readable = array_map(fn ($k) => $labels[$k] ?? $k, $keys);
                        return implode(', ', $readable) ?: '—';
                    })
                    ->wrap()
                    ->limit(80),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Module')
                    ->options([
                        'payroll'     => 'Paie',
                        'employee'    => 'Employé',
                        'declaration' => 'Déclaration',
                        'cnss_rate'   => 'Taux CNSS',
                    ]),
                SelectFilter::make('event')
                    ->label('Action')
                    ->options([
                        'created' => 'Créé',
                        'updated' => 'Modifié',
                        'deleted' => 'Supprimé',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    public static function canViewAny(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
