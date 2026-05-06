<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payroll extends Model
{
    use HasCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'salaire_brut', 'salaire_net', 'ir', 'total_cnss_employee'])
            ->logOnlyDirty()
            ->useLogName('payroll')
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'company_id',
        'employee_id',
        'month',
        'year',
        'salaire_brut',
        'overtime_hours',
        'overtime_amount',
        'is_prorata',
        'worked_days',
        'total_working_days',
        'total_cnss_employee',
        'total_cnss_employer',
        'amo_employee',
        'amo_employer',
        'ir',
        'salaire_net',
        'status',
        // Heures sup nuit
        'overtime_hours_night',
        'overtime_amount_night',
        // Absences sans solde
        'absence_days',
        'absence_deduction',
        // Snapshot taux
        'cnss_employee_rate',
        'cnss_employer_rate',
        'amo_employee_rate',
        'amo_employer_rate',
        'ir_rate_applied',
        // Prime ancienneté
        'anciennete_years',
        'anciennete_rate',
        'anciennete_amount',
    ];

    protected $casts = [
        'salaire_brut'        => 'decimal:2',
        'overtime_hours'      => 'decimal:2',
        'overtime_amount'     => 'decimal:2',
        'is_prorata'          => 'boolean',
        'total_cnss_employee' => 'decimal:2',
        'total_cnss_employer' => 'decimal:2',
        'amo_employee'        => 'decimal:2',
        'amo_employer'        => 'decimal:2',
        'ir'                  => 'decimal:2',
        'salaire_net'         => 'decimal:2',
        'overtime_hours_night' => 'decimal:2',
        'overtime_amount_night' => 'decimal:2',
        'absence_deduction'   => 'decimal:2',
        'cnss_employee_rate'  => 'decimal:4',
        'cnss_employer_rate'  => 'decimal:4',
        'amo_employee_rate'   => 'decimal:4',
        'amo_employer_rate'   => 'decimal:4',
        'ir_rate_applied'     => 'decimal:2',
        'anciennete_rate'     => 'decimal:2',
        'anciennete_amount'   => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(PayrollComponent::class);
    }

    public function isLocked(): bool
    {
        return $this->status === 'payé';
    }

    public function getPeriodeLabelAttribute(): string
    {
        $mois = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        return ($mois[$this->month] ?? $this->month) . ' ' . $this->year;
    }
}
