<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasCompanyScope;

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

    public function getPeriodeLabelAttribute(): string
    {
        $mois = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        return ($mois[$this->month] ?? $this->month) . ' ' . $this->year;
    }
}
