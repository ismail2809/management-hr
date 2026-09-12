<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\AutreDemandeResource;
use App\Models\DocumentRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AutreDemandesWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Autres demandes en attente';

    public static function canView(): bool
    {
        $user = auth()->user();
        return ! $user?->isBasicRole() && ! $user?->isExtendedRole();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DocumentRequest::query()
                    ->where('categorie', 'autre')
                    ->where('status', 'en_attente')
                    ->with('employee', 'processor')
                    ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employé')
                    ->weight('semibold')
                    ->searchable(['employees.first_name', 'employees.last_name']),
                TextColumn::make('type_label')
                    ->label('Type de demande')
                    ->badge()
                    ->color('info'),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->default('—'),
                TextColumn::make('created_at')
                    ->label('Soumise le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn () => 'En attente'),
            ])
            ->recordUrl(fn (DocumentRequest $record) => AutreDemandeResource::getUrl('view', ['record' => $record]))
            ->paginated(5)
            ->emptyStateHeading('Aucune demande en attente')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateDescription('Toutes les autres demandes ont été traitées.');
    }
}
