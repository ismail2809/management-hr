<?php

namespace App\Models\Traits;

use App\Models\Scopes\GlobalOrCompanyScope;

/**
 * Pour les modèles avec ecole_setting_id NULLABLE.
 * Retourne les enregistrements globaux + ceux de l'école courante.
 */
trait HasGlobalOrCompanyScope
{
    public static function bootHasGlobalOrCompanyScope(): void
    {
        static::addGlobalScope(new GlobalOrCompanyScope());
        static::creating(function ($model) {
            if (!array_key_exists('ecole_setting_id', $model->getAttributes()) && auth()->check() && auth()->user()?->ecole_setting_id) {
                $model->ecole_setting_id = auth()->user()->ecole_setting_id;
            }
        });
    }
}
