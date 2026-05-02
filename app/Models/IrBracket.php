<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IrBracket extends Model
{
    // Table globale — pas de company_id, pas de CompanyScope
    protected $fillable = [
        'min_salary',
        'max_salary',
        'rate_percentage',
        'deduction_amount',
    ];

    protected $casts = [
        'min_salary'       => 'decimal:2',
        'max_salary'       => 'decimal:2',
        'rate_percentage'  => 'decimal:2',
        'deduction_amount' => 'decimal:2',
    ];
}
