<?php

namespace App\Filament\App\Resources\AutreDemandeResource\Pages;

use App\Filament\App\Resources\AutreDemandeResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAutreDemande extends ViewRecord
{
    protected static string $resource = AutreDemandeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_final')
                ->label('Télécharger document')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->visible(fn () => $this->record->status === 'approuvé' && filled($this->record->fichier_final))
                ->url(fn () => asset('storage/' . $this->record->fichier_final))
                ->openUrlInNewTab(),

            EditAction::make(),
        ];
    }
}
