<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transport extends Model
{
    use HasCompanyScope;

    protected $fillable = ['company_id', 'name', 'matricule', 'type'];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function chauffeurs(): HasMany { return $this->hasMany(Employee::class); }
}
