<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AnneeScolaire extends Model
{
    use SoftDeletes, HasCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('annee_scolaire')
            ->dontSubmitEmptyLogs();
    }

    protected $table = 'annees_scolaires';

    protected $fillable = ['ecole_setting_id', 'name', 'is_active', 'note'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saved(function (AnneeScolaire $model) {
            if ($model->is_active) {
                static::withoutGlobalScopes()
                    ->where('ecole_setting_id', $model->ecole_setting_id)
                    ->where('id', '!=', $model->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(EcoleSettings::class, 'ecole_setting_id');
    }
}
