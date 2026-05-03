<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'employee_id',
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
