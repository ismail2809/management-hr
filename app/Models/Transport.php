<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transport extends Model
{
    use SoftDeletes, HasCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('transport')
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = ['company_id', 'name', 'matricule'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function chauffeurs(): HasMany { return $this->hasMany(Employee::class); }
}
