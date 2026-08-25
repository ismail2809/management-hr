<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use HasCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'status', 'contract_type', 'profession_id'])
            ->logOnlyDirty()
            ->useLogName('employee')
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'company_id',
        'profession_id',
        'profession_type',
        'matricule',
        'cin',
        'cnss_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_fixed',
        'birth_date',
        'birth_place',
        'hire_date',
        'diploma',
        'promotion',
        'exit_date',
        'exit_reason',
        'exit_comment',
        'contract_type',
        'marital_status',
        'number_of_children',
        'rib',
        'status',
        'photo',
        'address',
        'city',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date'  => 'date',
        'exit_date'  => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isProfesseur(): bool
    {
        return $this->profession?->name === 'Professeur';
    }

    public function company(): BelongsTo   { return $this->belongsTo(Company::class); }
    public function profession(): BelongsTo { return $this->belongsTo(Profession::class); }
    public function documents(): HasMany   { return $this->hasMany(EmployeeDocument::class); }
    public function leaves(): HasMany      { return $this->hasMany(Leave::class); }

    public function groupes(): BelongsToMany
    {
        return $this->belongsToMany(Groupe::class, 'employee_groupe');
    }
}
