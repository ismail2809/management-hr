<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAccident extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'ecole_setting_id',
        'employee_id',
        'date_accident',
        'rembourse',
        'montant',
        'description',
        'dossiers',
    ];

    protected $casts = [
        'date_accident' => 'datetime',
        'rembourse'     => 'boolean',
        'dossiers'      => 'array',
    ];

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}
