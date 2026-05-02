<?php

namespace App\Models\Traits;

use App\Models\Scopes\CompanyScope;

trait HasCompanyScope
{
    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());
        static::creating(function ($model) {
            if (!array_key_exists('company_id', $model->getAttributes())) {
                $model->company_id = auth()->user()?->company_id;
            }
        });
    }
}
