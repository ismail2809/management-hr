<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollComponent extends Model
{
    protected $fillable = [
        'payroll_id',
        'type',
        'label',
        'amount',
        'taxable',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'taxable' => 'boolean',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }
}
