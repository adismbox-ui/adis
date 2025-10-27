<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'titre', 'description', 'formateur_id', 'niveau_id', 'date_debut', 'date_fin', 'horaire', 'lien',
        'discipline', 'support', 'audio', 'prix', 'certificat'
    ];

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

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class);
    }

    public function sessions()
    {
        return $this->belongsToMany(\App\Models\SessionFormation::class, 'module_session_formation');
    }

    public function niveau()
    {
        return $this->belongsTo(\App\Models\Niveau::class);
    }

    public function formateur()
    {
        return $this->belongsTo(\App\Models\Formateur::class);
    }
}
