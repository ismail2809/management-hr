<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\DocumentAdministratifResource;
use App\Models\DocumentRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DocumentsAdministratifsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Documents administratifs en attente';

    public static function canView(): bool
    {
        return ! auth()->user()?->hasRole('employee');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DocumentRequest::query()
                    ->where('categorie', 'document')
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
                    ->label('Type de document')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('format')
                    ->label('Format')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => $state ? strtoupper($state) : '—'),
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
            ->recordUrl(fn (DocumentRequest $record) => DocumentAdministratifResource::getUrl('view', ['record' => $record]))
            ->paginated(5)
            ->emptyStateHeading('Aucun document en attente')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateDescription('Toutes les demandes de documents ont été traitées.');
    }
}
