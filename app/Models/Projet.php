<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    protected $fillable = [
        'intitule',
        'beneficiaires',
        'objectif',
        'description',
        'debut',
        'fin_prevue',
        'taux_avancement',
        'responsable',
        'statut',
        'montant_total',
        'montant_collecte',
        'reste_a_financer',
        'date_limite'
    ];

    protected $casts = [
        'debut' => 'date',
        'fin_prevue' => 'date',
        'date_limite' => 'date',
        'taux_avancement' => 'integer',
        'montant_total' => 'decimal:2',
        'montant_collecte' => 'decimal:2',
        'reste_a_financer' => 'decimal:2'
    ];

    // Relations
    public function dons()
    {
        return $this->hasMany(Don::class);
    }

    public function rapports()
    {
        return $this->hasMany(Rapport::class);
    }

    public function galeries()
    {
        return $this->hasMany(Galerie::class);
    }

    // Accesseurs
    public function getProgressPercentageAttribute()
    {
        return $this->taux_avancement ?? 0;
    }

    public function getRemainingAmountAttribute()
    {
        if ($this->montant_total && $this->montant_collecte) {
            return $this->montant_total - $this->montant_collecte;
        }
        return 0;
    }

    public function getFundingPercentageAttribute()
    {
        if ($this->montant_total && $this->montant_collecte) {
            return round(($this->montant_collecte / $this->montant_total) * 100, 2);
        }
        return 0;
    }

    public function getStatusLabelAttribute()
    {
        return match($this->statut) {
            'en_cours' => 'En cours',
            'realise' => 'Réalisé',
            'a_financer' => 'À financer',
            'en_attente' => 'En attente',
            default => $this->statut
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->statut) {
            'en_cours' => 'primary',
            'realise' => 'success',
            'a_financer' => 'warning',
            'en_attente' => 'secondary',
            default => 'secondary'
        };
    }

    // Scopes
    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeRealises($query)
    {
        return $query->where('statut', 'realise');
    }

    public function scopeAFinancer($query)
    {
        return $query->where('statut', 'a_financer');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }
}
