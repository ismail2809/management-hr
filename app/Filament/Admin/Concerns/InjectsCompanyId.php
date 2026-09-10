<?php

namespace App\Filament\App\Concerns;

/**
 * À ajouter sur toutes les pages CreateRecord du panel app.
 * Injecte company_id depuis l'utilisateur Filament connecté,
 * sans dépendre de auth() qui peut retourner le mauvais user
 * quand les deux panels partagent la même session web.
 */
trait InjectsCompanyId
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = \Filament\Facades\Filament::auth()->user();
        if (empty($data['company_id'])) {
            $data['company_id'] = $user?->company_id;
        }
        return $data;
    }
}
