<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Scope pour les tables à ecole_setting_id NULLABLE.
 * Retourne les enregistrements globaux (ecole_setting_id IS NULL)
 * ET ceux de l'école courante.
 */
class GlobalOrCompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $table          = $model->getTable();
            $ecoleSettingId = auth()->user()->ecole_setting_id;

            $builder->where(function ($q) use ($table, $ecoleSettingId) {
                $q->whereNull($table . '.ecole_setting_id')
                  ->orWhere($table . '.ecole_setting_id', $ecoleSettingId);
            });
        }
    }
}
