<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'employee_id',
        'leave_type_id',
        'year',
        'entitled_days',
        'used_days',
    ];

    protected $casts = [
        'entitled_days'  => 'decimal:1',
        'used_days'      => 'decimal:1',
        'remaining_days' => 'decimal:1',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Recalcule used_days depuis les congés approuvés et met à jour le solde.
     */
    public static function recalculate(int $employeeId, int $leaveTypeId, int $year): void
    {
        $usedDays = Leave::withoutGlobalScopes()
            ->where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approuvé')
            ->whereYear('start_date', $year)
            ->get()
            ->sum(fn ($l) => $l->duration_days);

        self::withoutGlobalScopes()
            ->where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->update(['used_days' => $usedDays]);
    }

    /**
     * Initialise ou retrouve le solde pour un employé/type/année.
     */
    public static function findOrInit(int $companyId, int $employeeId, int $leaveTypeId, int $year): self
    {
        return self::withoutGlobalScopes()->firstOrCreate(
            ['company_id' => $companyId, 'employee_id' => $employeeId, 'leave_type_id' => $leaveTypeId, 'year' => $year],
            ['entitled_days' => 0, 'used_days' => 0]
        );
    }
}
