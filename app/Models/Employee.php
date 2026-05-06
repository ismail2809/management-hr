<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use HasCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'status', 'department_id', 'position_id', 'contract_type'])
            ->logOnlyDirty()
            ->useLogName('employee')
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'company_id',
        'matricule',
        'cin',
        'cnss_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'hire_date',
        'contract_type',
        'marital_status',
        'number_of_children',
        'department_id',
        'position_id',
        'rib',
        'status',
        'photo',
        'address',
        'city',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date'  => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function contracts(): HasMany { return $this->hasMany(Contract::class); }
    public function documents(): HasMany { return $this->hasMany(EmployeeDocument::class); }
    public function leaves(): HasMany { return $this->hasMany(Leave::class); }
}
