<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'ice',
        'rc',
        'patente',
        'cnss_affiliation',
        'city',
        'email',
        'phone',
    ];
}
