<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'ordre',
        'actif',
        'formateur_id',
        'lien_meet',
        'session_id'
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function sessionsFormation()
    {
        return $this->hasMany(SessionFormation::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function formateur()
    {
        return $this->belongsTo(\App\Models\Formateur::class);
    }

    public function sessionFormation()
    {
        return $this->belongsTo(\App\Models\SessionFormation::class, 'session_id');
    }
} 