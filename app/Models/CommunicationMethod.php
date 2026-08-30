<?php

namespace App\Models;

use App\Models\Traits\HasGlobalOrCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationMethod extends Model
{
    use HasGlobalOrCompanyScope;

    protected $fillable = ['company_id', 'name', 'code', 'active', 'sort_order'];

    protected $casts = ['active' => 'boolean'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
