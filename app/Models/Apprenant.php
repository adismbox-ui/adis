<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apprenant extends Model
{
    protected $fillable = [
        'utilisateur_id',
        'niveau_id',
        'connaissance_adis',
        'formation_adis',
        'formation_autre',
        'niveau_coran',
        'niveau_arabe',
        'connaissance_tomes_medine',
        'tomes_medine_etudies',
        'disciplines_souhaitees',
        'attentes',
        'formateur_domicile',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function certificats()
    {
        return $this->hasMany(Certificat::class);
    }

    public function niveau()
    {
        return $this->belongsTo(\App\Models\Niveau::class);
    }
}
