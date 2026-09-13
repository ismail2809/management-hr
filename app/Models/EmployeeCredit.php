<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeCredit extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'ecole_setting_id',
        'employee_id',
        'types',
        'montant',
        'mensualite',
        'notes',
    ];

    protected $casts = [
        'types' => 'array',
    ];

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}
