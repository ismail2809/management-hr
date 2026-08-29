<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('company')
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'name',
        'ice',
        'rc',
        'patente',
        'cnss_affiliation',
        'city',
        'email',
        'phone',
    ];
}
