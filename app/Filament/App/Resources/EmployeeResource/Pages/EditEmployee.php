<?php

namespace App\Filament\App\Resources\EmployeeResource\Pages;

use App\Filament\App\Resources\EmployeeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ReplicateAction::make()
                ->label('Dupliquer')
                ->icon('heroicon-o-document-duplicate')
                ->excludeAttributes(['first_name', 'last_name', 'email', 'cin', 'matricule', 'photo', 'cnss_number', 'rib'])
                ->beforeReplicaSaved(function ($replica) {
                    $replica->first_name  = null;
                    $replica->last_name   = null;
                    $replica->email       = null;
                    $replica->cin         = null;
                    $replica->matricule   = null;
                    $replica->photo       = null;
                    $replica->cnss_number = null;
                    $replica->rib         = null;
                })
                ->redirectTo(fn ($replica) => EmployeeResource::getUrl('edit', ['record' => $replica])),

            DeleteAction::make(),
        ];
    }
}
