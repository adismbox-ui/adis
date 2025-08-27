<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partenaire extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'email',
        'telephone',
        'site_web',
        'logo',
    ];
}
