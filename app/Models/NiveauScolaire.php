<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NiveauScolaire extends Model
{
    use HasCompanyScope;

    protected $table = 'niveaux_scolaires';
    protected $fillable = ['company_id', 'name', 'order'];

    public function groupes(): HasMany
    {
        return $this->hasMany(Groupe::class);
    }
}
