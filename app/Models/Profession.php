<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profession extends Model
{
    use HasCompanyScope;

    protected $fillable = ['company_id', 'name'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
