<?php

namespace App\Filament\Admin\Resources\AutreDemandeResource\Pages;

use App\Filament\Admin\Resources\AutreDemandeResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAutreDemande extends ViewRecord
{
    protected static string $resource = AutreDemandeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_joint')
                ->label('Télécharger pièce jointe')
                ->icon('heroicon-o-paper-clip')
                ->color('primary')
                ->visible(fn () => filled($this->record->fichier_joint))
                ->url(fn () => asset('storage/' . $this->record->fichier_joint))
                ->openUrlInNewTab(),

            EditAction::make(),
        ];
    }
}
