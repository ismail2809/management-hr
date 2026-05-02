<?php

namespace App\Models;

use App\Models\Traits\HasGlobalOrCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CnssRate extends Model
{
    use HasGlobalOrCompanyScope;

    protected $fillable = [
        'company_id',
        'type',
        'label',
        'rate_percentage',
        'plafond',
    ];

    protected $casts = [
        'rate_percentage' => 'decimal:2',
        'plafond'         => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
