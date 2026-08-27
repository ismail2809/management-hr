<?php

namespace App\Models;

use App\Models\Traits\HasGlobalOrCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentType extends Model
{
    use HasGlobalOrCompanyScope;

    protected $fillable = ['company_id', 'name', 'code', 'categorie', 'active', 'sort_order'];

    protected $casts = ['active' => 'boolean'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
