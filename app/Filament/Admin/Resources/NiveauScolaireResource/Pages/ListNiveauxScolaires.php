<?php
namespace App\Filament\App\Resources\NiveauScolaireResource\Pages;
use App\Filament\App\Resources\NiveauScolaireResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListNiveauxScolaires extends ListRecords {
    protected static string $resource = NiveauScolaireResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
