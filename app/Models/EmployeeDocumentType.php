<?php

namespace App\Models;

use App\Models\Traits\HasGlobalOrCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocumentType extends Model
{
    use SoftDeletes, HasGlobalOrCompanyScope;

    protected $fillable = ['ecole_setting_id', 'name', 'code', 'active', 'sort_order'];

    protected $casts = ['active' => 'boolean'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(EcoleSettings::class, 'ecole_setting_id');
    }
}
