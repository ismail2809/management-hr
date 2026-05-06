<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasCompanyScope;

    public const CATEGORIES = [
        'Enseignement' => 'Enseignement',
        'Administration' => 'Administration',
        'Support' => 'Support',
        'Transport' => 'Transport',
    ];

    protected $fillable = ['company_id', 'title', 'category', 'base_salary'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
