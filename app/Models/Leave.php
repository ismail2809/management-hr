<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'employee_id',
        'categorie',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'justificatif',
        'remplacant_id',
        'type_cours',
        'nb_pages',
        'intitule_lecon',
        'intitule_activite',
        'status',
        'approved_by',
        'approved_at',
        'severity_level',
        'communication_method',
        'appointment_date',
        'actions_taken',
        'rh_notes',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'approved_at'      => 'datetime',
        'appointment_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $leave) {
            if ($leave->employee_id && $leave->leave_type_id && $leave->start_date) {
                LeaveBalance::findOrInit(
                    $leave->company_id,
                    $leave->employee_id,
                    $leave->leave_type_id,
                    $leave->start_date->year
                );
                LeaveBalance::recalculate(
                    $leave->employee_id,
                    $leave->leave_type_id,
                    $leave->start_date->year
                );
            }
        });
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInWeekdays($this->end_date) + 1;
    }

    public function company(): BelongsTo   { return $this->belongsTo(Company::class); }
    public function employee(): BelongsTo  { return $this->belongsTo(Employee::class); }
    public function leaveType(): BelongsTo { return $this->belongsTo(LeaveType::class); }
    public function approver(): BelongsTo  { return $this->belongsTo(User::class, 'approved_by'); }
    public function remplacant(): BelongsTo { return $this->belongsTo(Employee::class, 'remplacant_id'); }
}
