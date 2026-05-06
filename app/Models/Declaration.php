<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Declaration extends Model
{
    use HasCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'type', 'month', 'year'])
            ->logOnlyDirty()
            ->useLogName('declaration')
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'company_id',
        'type',
        'month',
        'year',
        'generated_file_path',
        'status',
        'employee_count',
        'total_brut',
        'total_cnss_employee',
        'total_cnss_employer',
        'total_amo_employee',
        'total_amo_employer',
        'total_ir',
        'total_net',
    ];

    protected $casts = [
        'total_brut'           => 'decimal:2',
        'total_cnss_employee'  => 'decimal:2',
        'total_cnss_employer'  => 'decimal:2',
        'total_amo_employee'   => 'decimal:2',
        'total_amo_employer'   => 'decimal:2',
        'total_ir'             => 'decimal:2',
        'total_net'            => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getPeriodeLabelAttribute(): string
    {
        $mois = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        return ($mois[$this->month] ?? $this->month) . ' ' . $this->year;
    }
}
