<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LienSocial extends Model
{
    protected $table = 'liens_sociaux';
    
    protected $fillable = [
        'plateforme',
        'titre',
        'description',
        'url',
        'actif',
        'ordre',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
    ];
}

