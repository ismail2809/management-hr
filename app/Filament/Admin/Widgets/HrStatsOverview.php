<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\AutreDemandeResource;
use App\Filament\Admin\Resources\DocumentAdministratifResource;
use App\Filament\Admin\Resources\LeaveResource;
use App\Models\DocumentRequest;
use App\Models\Leave;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return ! auth()->user()?->isBasicRole();
    }

    protected function getStats(): array
    {
        $congesEnAttente         = Leave::where('status', 'en_attente')->count();
        $absencesEnAttente       = Leave::where('categorie', 'absence')->where('status', 'en_attente')->count();
        $docsEnAttente           = DocumentRequest::where('categorie', 'document')->where('status', 'en_attente')->count();
        $autresDemandesEnAttente = DocumentRequest::where('categorie', 'autre')->where('status', 'en_attente')->count();

        return [
            Stat::make('Documents administratifs en attente', $docsEnAttente)
                ->description($docsEnAttente > 0 ? 'À traiter rapidement' : 'Aucune demande')
                ->descriptionIcon($docsEnAttente > 0 ? 'heroicon-o-document-text' : 'heroicon-o-check-circle')
                ->color($docsEnAttente > 0 ? 'warning' : 'success')
                ->url(DocumentAdministratifResource::getUrl('index') . '?tableFilters[status][value]=en_attente'),

            Stat::make('Autres demandes en attente', $autresDemandesEnAttente)
                ->description($autresDemandesEnAttente > 0 ? 'À traiter rapidement' : 'Aucune demande')
                ->descriptionIcon($autresDemandesEnAttente > 0 ? 'heroicon-o-inbox' : 'heroicon-o-check-circle')
                ->color($autresDemandesEnAttente > 0 ? 'warning' : 'success')
                ->url(AutreDemandeResource::getUrl('index') . '?tableFilters[status][value]=en_attente'),

            Stat::make('Congés en attente', $congesEnAttente)
                ->description($congesEnAttente > 0 ? 'À traiter rapidement' : 'Aucune demande')
                ->descriptionIcon($congesEnAttente > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->color($congesEnAttente > 0 ? 'warning' : 'success')
                ->url(LeaveResource::getUrl('index') . '?tableFilters[status][value]=en_attente'),

            Stat::make('Absences en attente', $absencesEnAttente)
                ->description($absencesEnAttente > 0 ? 'À traiter rapidement' : 'Aucune absence en attente')
                ->descriptionIcon($absencesEnAttente > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->color($absencesEnAttente > 0 ? 'warning' : 'success')
                ->url(LeaveResource::getUrl('index') . '?tableFilters[status][value]=en_attente'),
        ];
    }
}
