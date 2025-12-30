<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formateur extends Model
{
    protected $fillable = [
        'utilisateur_id', 
        'valide', 
        'validation_token', 
        'specialite',
        'connaissance_adis',
        'formation_adis',
        'formation_autre',
        'niveau_coran',
        'niveau_arabe',
        'niveau_francais',
        'diplome_religieux',
        'diplome_general',
        'fichier_diplome_religieux',
        'fichier_diplome_general',
        'ville', 
        'commune', 
        'quartier'
    ];

    protected $casts = [
        'formation_adis' => 'boolean',
        'formation_autre' => 'boolean',
        'valide' => 'boolean',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function modules()
    {
        return $this->hasMany(\App\Models\Module::class, 'formateur_id');
    }

    public function niveaux()
    {
        return $this->hasMany(\App\Models\Niveau::class, 'formateur_id');
    }

    public function assistant()
    {
        return $this->hasOne(\App\Models\Assistant::class, 'formateur_id');
    }
}
