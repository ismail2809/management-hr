<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Groupe extends Model
{
    use HasCompanyScope;

    protected $fillable = ['company_id', 'niveau_scolaire_id', 'name'];

    public function niveauScolaire(): BelongsTo
    {
        return $this->belongsTo(NiveauScolaire::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_groupe');
    }
}
