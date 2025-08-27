<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionFormation extends Model
{
    use HasFactory;

    protected $table = 'sessions_formation';

    protected $fillable = [
        'nom',
        'description',
        'niveau_id',
        'formateur_id',
        'date_debut',
        'date_fin',
        'heure_debut',
        'heure_fin',
        'jour_semaine',
        'duree_seance_minutes',
        'nombre_seances',
        'prix',
        'places_max',
        'actif'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'prix' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    public function modules()
    {
        return $this->belongsToMany(\App\Models\Module::class, 'module_session_formation');
    }

    public function formateur()
    {
        return $this->belongsTo(\App\Models\Formateur::class);
    }

    public function getNombreInscritsAttribute()
    {
        return $this->inscriptions()->where('statut', 'valide')->count();
    }

    public function getPlacesDisponiblesAttribute()
    {
        if ($this->places_max === null) {
            return null; // Pas de limite
        }
        return max(0, $this->places_max - $this->nombre_inscrits);
    }

    public function getCompletAttribute()
    {
        if ($this->places_max === null) {
            return false;
        }
        return $this->nombre_inscrits >= $this->places_max;
    }
} 