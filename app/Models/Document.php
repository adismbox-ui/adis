<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['titre', 'type', 'fichier', 'module_id', 'certificat_id', 'formateur_id', 'created_by_admin', 'niveau_id', 'semaine', 'audio', 'session_id', 'date_envoi', 'envoye'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function certificat()
    {
        return $this->belongsTo(Certificat::class);
    }

    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }

    public function niveau()
    {
        return $this->belongsTo(\App\Models\Niveau::class);
    }
    
    public function session()
    {
        return $this->belongsTo(SessionFormation::class, 'session_id');
    }
}
