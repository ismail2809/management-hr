<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EcoleSettings extends Model
{
    protected $table = 'ecole_settings';

    protected $fillable = [
        'company_id',
        'nom_ecole',
        'code_massare',
        'cnss',
        'patente',
        'rc',
        'if_number',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'telephone',
        'fax',
        'email',
        'site_web',
        'annee_scolaire',
        'logo',
        'cachet',
        'afficher_logo_pdf',
        'afficher_cachet_pdf',
        'entete_document',
        'pied_document',
    ];

    protected $casts = [
        'afficher_logo_pdf'   => 'boolean',
        'afficher_cachet_pdf' => 'boolean',
    ];

    /**
     * Récupérer les paramètres de l'école pour la company courante (singleton par company)
     */
    public static function get(): self
    {
        $companyId = auth()->user()?->company_id
            ?? \App\Models\Company::value('id');

        return self::firstOrCreate(
            ['company_id' => $companyId],
            [
                'nom_ecole'           => 'Mon École',
                'pays'                => 'Maroc',
                'afficher_logo_pdf'   => true,
                'afficher_cachet_pdf' => true,
            ]
        );
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function getLogoPathAttribute(): ?string
    {
        return $this->logo ? storage_path('app/public/' . $this->logo) : null;
    }

    public function getCachetUrlAttribute(): ?string
    {
        return $this->cachet ? Storage::url($this->cachet) : null;
    }

    public function getCachetPathAttribute(): ?string
    {
        return $this->cachet ? storage_path('app/public/' . $this->cachet) : null;
    }

    public function getAdresseCompleteAttribute(): string
    {
        $parts = array_filter([
            $this->adresse,
            trim($this->code_postal . ' ' . $this->ville),
            $this->pays,
        ]);

        return implode("\n", $parts);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
