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
        return ! auth()->user()?->hasRole('employee');
    }

    protected function getStats(): array
    {
        $congesEnAttente         = Leave::where('status', 'en_attente')->count();
        $absentsAujourdhui       = Leave::where('status', 'approuvé')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();
        $docsEnAttente           = DocumentRequest::where('categorie', 'document')->where('status', 'en_attente')->count();
        $autresDemandesEnAttente = DocumentRequest::where('categorie', 'autre')->where('status', 'en_attente')->count();

        return [
            Stat::make('Congés en attente', $congesEnAttente)
                ->description($congesEnAttente > 0 ? 'À traiter rapidement' : 'Aucune demande')
                ->descriptionIcon($congesEnAttente > 0 ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                ->color($congesEnAttente > 0 ? 'warning' : 'success')
                ->url(LeaveResource::getUrl('index') . '?tableFilters[status][value]=en_attente'),

            Stat::make("Absents aujourd'hui", $absentsAujourdhui)
                ->description($absentsAujourdhui > 0 ? 'Absences approuvées' : 'Tout le monde est présent')
                ->descriptionIcon($absentsAujourdhui > 0 ? 'heroicon-o-user-minus' : 'heroicon-o-user-group')
                ->color($absentsAujourdhui > 0 ? 'danger' : 'success')
                ->url(LeaveResource::getUrl('index') . '?tableFilters[status][value]=approuv%C3%A9'),

            Stat::make('Documents administratifs en attente', $docsEnAttente)
                ->description($docsEnAttente > 0 ? 'Demandes à traiter' : 'Aucune demande')
                ->descriptionIcon($docsEnAttente > 0 ? 'heroicon-o-document-text' : 'heroicon-o-check-circle')
                ->color($docsEnAttente > 0 ? 'warning' : 'success')
                ->url(DocumentAdministratifResource::getUrl('index') . '?tableFilters[status][value]=en_attente'),

            Stat::make('Autres demandes en attente', $autresDemandesEnAttente)
                ->description($autresDemandesEnAttente > 0 ? 'Demandes à traiter' : 'Aucune demande')
                ->descriptionIcon($autresDemandesEnAttente > 0 ? 'heroicon-o-inbox' : 'heroicon-o-check-circle')
                ->color($autresDemandesEnAttente > 0 ? 'warning' : 'success')
                ->url(AutreDemandeResource::getUrl('index') . '?tableFilters[status][value]=en_attente'),
        ];
    }
}
