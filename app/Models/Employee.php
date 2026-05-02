<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasCompanyScope;

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

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
