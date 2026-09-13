<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFondationM6 extends Model
{
    use HasCompanyScope;

    protected $table = 'employee_fondation_m6';

    protected $fillable = [
        'ecole_setting_id',
        'employee_id',
        'annee_scolaire',
        'fichiers',
        'notes',
    ];

    protected $casts = [
        'fichiers' => 'array',
    ];

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}
