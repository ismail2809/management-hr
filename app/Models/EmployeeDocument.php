<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeDocument extends Model
{
    use SoftDeletes, HasCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type_document', 'name', 'uploaded_by', 'employee_id'])
            ->logOnlyDirty()
            ->useLogName('employee_document')
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'company_id',
        'employee_id',
        'type_document',
        'name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
