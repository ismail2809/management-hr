<?php

namespace App\Models\Traits;

use App\Models\Scopes\CompanyScope;

trait HasCompanyScope
{
    public static function bootHasCompanyScope(): void
    {
        static::addGlobalScope(new CompanyScope());
        static::creating(function ($model) {
            if (is_null($model->ecole_setting_id)) {
                $authUser     = auth()->user();
                $filamentUser = null;
                try { $filamentUser = \Filament\Facades\Filament::auth()->user(); } catch (\Throwable $e) {}

                $user = $authUser ?? $filamentUser;
                $model->ecole_setting_id = $user?->ecole_setting_id;
            }
        });
    }
}
