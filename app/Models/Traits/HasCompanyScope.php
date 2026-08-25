<?php

namespace App\Models\Traits;

use App\Models\Scopes\CompanyScope;

trait HasCompanyScope
{
    public static function bootHasCompanyScope(): void
    {
        static::addGlobalScope(new CompanyScope());
        static::creating(function ($model) {
            if (is_null($model->company_id)) {
                $authUser     = auth()->user();
                $filamentUser = null;
                try { $filamentUser = \Filament\Facades\Filament::auth()->user(); } catch (\Throwable $e) {}

                \Illuminate\Support\Facades\Log::debug('HasCompanyScope::creating', [
                    'model'           => get_class($model),
                    'auth_user'       => $authUser?->email,
                    'auth_company_id' => $authUser?->company_id,
                    'fil_user'        => $filamentUser?->email,
                    'fil_company_id'  => $filamentUser?->company_id,
                ]);

                $user = $authUser ?? $filamentUser;
                $model->company_id = $user?->company_id;
            }
        });
    }
}
