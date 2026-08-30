<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
    use HasCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'approved_by', 'approved_at', 'rh_notes'])
            ->logOnlyDirty()
            ->useLogName('leave')
            ->dontSubmitEmptyLogs();
    }

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
