<?php

namespace App\Models;

use App\Models\Traits\HasGlobalOrCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentType extends Model
{
    use SoftDeletes;

    use HasGlobalOrCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('document_type')
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = ['company_id', 'name', 'code', 'categorie', 'active', 'sort_order'];

    protected $casts = ['active' => 'boolean'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
