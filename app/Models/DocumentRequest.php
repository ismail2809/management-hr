<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequest extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'employee_id',
        'type',
        'format',
        'reason',
        'status',
        'generated_file_path',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public static array $typeLabels = [
        'attestation_travail'  => 'Attestation de travail',
        'attestation_salaire'  => 'Attestation de salaire',
        'attestation_cnss'     => 'Attestation CNSS',
        'ordre_mission'        => 'Ordre de mission',
        'certificat_travail'   => 'Certificat de travail',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class, 'processed_by'); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
