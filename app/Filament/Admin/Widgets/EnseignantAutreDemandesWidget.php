<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\AutreDemandeResource;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EnseignantAutreDemandesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Mes autres demandes';

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
                    ->where('categorie', 'autre')
                    ->whereIn('status', ['en_attente', 'approuvé'])
                    ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('type')
                    ->label('Type de demande')
                    ->formatStateUsing(fn ($state) => DocumentRequest::$autreTypes[$state]
                        ?? DocumentType::withoutGlobalScopes()->where('code', $state)->value('name')
                        ?? $state)
                    ->badge()
                    ->color('info'),
                TextColumn::make('date_souhaitee')
                    ->label('Date souhaitée')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : '—'),
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
            ->recordUrl(fn (DocumentRequest $record) => AutreDemandeResource::getUrl('view', ['record' => $record]))
            ->paginated(5)
            ->emptyStateHeading('Aucune autre demande en cours')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateDescription('Vous n\'avez pas d\'autres demandes en attente.');
    }
}
