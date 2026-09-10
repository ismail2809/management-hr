<?php

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\App\Concerns\InjectsCompanyId;

class CreateUser extends CreateRecord
{
    use InjectsCompanyId;

    protected static string $resource = UserResource::class;

    // Injecter le company_id sauf si super-admin (qui le choisit manuellement)
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->hasRole('super-admin')) {
            $data['company_id'] = auth()->user()?->company_id;
        }
        return $data;
    }

    protected function afterCreate(): void
    {
        $role = $this->data['roles'] ?? 'employee';
        $this->record->syncRoles([$role]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
