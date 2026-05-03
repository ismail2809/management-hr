<?php

namespace App\Filament\Admin\Resources\EmployeeResource\Pages;

use App\Filament\Admin\Resources\EmployeeResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    public function getTitle(): string
    {
        return 'Nouvel employé';
    }

    public function getSubheading(): ?string
    {
        return 'Remplissez les informations pour créer un nouveau dossier employé.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retour')
                ->label('Retour à la liste')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(EmployeeResource::getUrl('index')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Employé créé avec succès';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['matricule'])) {
            $count = \App\Models\Employee::withoutGlobalScopes()->count() + 1;
            $data['matricule'] = 'EMP' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        return $data;
    }
}
