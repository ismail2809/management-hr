<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Scope pour les tables à company_id NULLABLE.
 * Retourne les enregistrements globaux (company_id IS NULL)
 * ET ceux de la company courante.
 */
class GlobalOrCompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $table = $model->getTable();
            $companyId = auth()->user()->company_id;

            $builder->where(function ($q) use ($table, $companyId) {
                $q->whereNull($table . '.company_id')
                  ->orWhere($table . '.company_id', $companyId);
            });
        }
    }
}
