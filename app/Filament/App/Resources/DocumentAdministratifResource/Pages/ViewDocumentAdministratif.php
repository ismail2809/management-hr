<?php

namespace App\Filament\App\Resources\DocumentAdministratifResource\Pages;

use App\Filament\App\Resources\DocumentAdministratifResource;
use App\Models\DocumentRequest;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDocumentAdministratif extends ViewRecord
{
    protected static string $resource = DocumentAdministratifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview_pdf')
                ->label('Voir PDF')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->visible(fn () => $this->record->format === 'digital' && filled($this->record->generated_file_path))
                ->url(fn () => route('documents.preview', $this->record))
                ->openUrlInNewTab(),

            Action::make('generate_pdf')
                ->label('Télécharger PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->visible(fn () => $this->record->format === 'digital' && filled($this->record->generated_file_path))
                ->url(fn () => route('documents.pdf', $this->record))
                ->openUrlInNewTab(),

            EditAction::make(),
        ];
    }
}
