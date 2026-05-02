<?php

namespace App\Filament\App\Resources\DocumentRequestResource\Pages;

use App\Filament\App\Resources\DocumentRequestResource;
use App\Models\DocumentRequest;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDocumentRequest extends ViewRecord
{
    protected static string $resource = DocumentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_pdf')
                ->label('Générer PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->visible(fn () => $this->record->format === 'digital')
                ->url(fn () => route('documents.pdf', $this->record))
                ->openUrlInNewTab(),

            EditAction::make(),
        ];
    }
}
