<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentRequest extends Model
{
    use SoftDeletes, HasCompanyScope, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'type', 'format', 'processed_by', 'processed_at', 'fichier_final'])
            ->logOnlyDirty()
            ->useLogName('document_request')
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'company_id',
        'employee_id',
        'categorie',
        'type',
        'format',
        'reason',
        'description',
        'status',
        'generated_file_path',
        'fichier_final',
        'nb_telechargements',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public static array $documentTypes = [
        'attestation_travail'  => 'Attestation de travail',
        'attestation_salaire'  => 'Attestation de salaire',
        'bulletin_paie'        => 'Bulletin de paie',
        'attestation_ir'       => 'Attestation IR',
        'credit_irrevocable'   => 'Crédit irrévocable',
        'attestation_cnss'     => 'Attestation CNSS',
        'ordre_mission'        => 'Ordre de mission',
        'certificat_travail'   => 'Certificat de travail',
    ];

    public static array $autreTypes = [
        'materiel'             => 'Matériel',
        'grande_salle'         => 'Grande salle',
        'photocopie'           => 'Photocopie',
        'rencontre_parents'    => 'Rencontre parents',
        'rencontre_direction'  => 'Rencontre direction',
        'formation'            => 'Formation',
        'activites'            => 'Activités',
        'divers'               => 'Divers',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$documentTypes[$this->type]
            ?? self::$autreTypes[$this->type]
            ?? $this->type;
    }

    public function employee(): BelongsTo  { return $this->belongsTo(Employee::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class, 'processed_by'); }
    public function company(): BelongsTo   { return $this->belongsTo(Company::class); }
}
