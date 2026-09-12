<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DocumentRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EnseignantDemandesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Mes demandes en cours';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['enseignant', 'enseignante']);
    }

    public function table(Table $table): Table
    {
        $employeeId = auth()->user()?->employee_id;

        return $table
            ->query(
                DocumentRequest::query()
                    ->where('employee_id', $employeeId)
                    ->whereIn('status', ['en_attente', 'approuvé'])
                    ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('categorie')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn ($state) => $state === 'document' ? 'primary' : 'info')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'document' => 'Document',
                        'autre'    => 'Autre demande',
                        default    => $state,
                    }),
                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => \App\Models\DocumentRequest::$autreTypes[$state]
                        ?? \App\Models\DocumentType::withoutGlobalScopes()->where('code', $state)->value('name')
                        ?? $state),
                TextColumn::make('date_souhaitee')
                    ->label('Date souhaitée')
                    ->date('d/m/Y')
                    ->default('—'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'en_attente' => 'warning',
                        'approuvé'   => 'success',
                        'refusé'     => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'en_attente' => 'En attente',
                        'approuvé'   => 'Approuvé',
                        'refusé'     => 'Refusé',
                        default      => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Soumise le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->paginated(5)
            ->emptyStateHeading('Aucune demande en cours')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateDescription('Vous n\'avez pas de demandes en attente.');
    }
}
