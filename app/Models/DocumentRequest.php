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
        'ecole_setting_id',
        'employee_id',
        'categorie',
        'type',
        'date_souhaitee',
        'format',
        'reason',
        'description',
        'fichier_joint',
        'status',
        'generated_file_path',
        'fichier_final',
        'nb_telechargements',
        'processed_by',
        'processed_at',
        'photocopie_sous_type',
        'photocopie_niveau',
        'photocopie_groupe',
        'photocopie_nb_copies',
        'photocopie_date_souhaitee',
        'rencontre_employee_ids',
    ];

    protected function castAttribute($key, $value)
    {
        if (in_array($key, ['processed_at', 'date_souhaitee', 'photocopie_date_souhaitee']) && $value !== null) {
            try {
                return parent::castAttribute($key, $value);
            } catch (\Throwable) {
                return null;
            }
        }
        return parent::castAttribute($key, $value);
    }

    protected $casts = [
        'processed_at'              => 'datetime',
        'date_souhaitee'            => 'date',
        'photocopie_date_souhaitee' => 'date',
        'photocopie_nb_copies'      => 'integer',
        'rencontre_employee_ids'    => 'array',
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

    public function employee(): BelongsTo        { return $this->belongsTo(Employee::class); }
    public function processor(): BelongsTo       { return $this->belongsTo(User::class, 'processed_by'); }
    public function company(): BelongsTo          { return $this->belongsTo(EcoleSettings::class, 'ecole_setting_id'); }
    public function ecoleSettings(): BelongsTo   { return $this->belongsTo(EcoleSettings::class, 'ecole_setting_id'); }
}
