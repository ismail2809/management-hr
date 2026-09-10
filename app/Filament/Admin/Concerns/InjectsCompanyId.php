<?php

namespace App\Filament\Admin\Concerns;

trait InjectsCompanyId
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = \Filament\Facades\Filament::auth()->user();
        if (empty($data['ecole_setting_id'])) {
            $data['ecole_setting_id'] = $user?->ecole_setting_id;
        }
        return $data;
    }
}
