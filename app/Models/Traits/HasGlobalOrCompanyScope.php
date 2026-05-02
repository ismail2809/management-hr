<?php

namespace App\Models\Traits;

use App\Models\Scopes\GlobalOrCompanyScope;

/**
 * Pour les modèles avec company_id NULLABLE (ex: CnssRate).
 * Retourne les enregistrements globaux + ceux de la company courante.
 * Lors de la création, injecte company_id seulement si l'user a une company.
 */
trait HasGlobalOrCompanyScope
{
    protected static function booted(): void
    {
        static::addGlobalScope(new GlobalOrCompanyScope());
        static::creating(function ($model) {
            // N'injecte company_id que si la clé est ABSENTE (pas si null explicitement fourni)
            if (!array_key_exists('company_id', $model->getAttributes()) && auth()->check() && auth()->user()?->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }
}
