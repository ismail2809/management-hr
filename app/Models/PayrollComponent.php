<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollComponent extends Model
{
    // Types fixes : plus de toggle libre "Imposable"
    public const TYPES = [
        'prime_imposable'     => 'Prime imposable',
        'prime_non_imposable' => 'Prime non imposable',
        'avantage_imposable'  => 'Avantage imposable',
        'retenue'             => 'Retenue',
    ];

    protected $fillable = [
        'payroll_id',
        'type',
        'label',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /** Déduit si la composante entre dans la base imposable. */
    public function isImposable(): bool
    {
        return in_array($this->type, ['prime_imposable', 'avantage_imposable']);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }
}
