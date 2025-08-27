<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $fillable = [
        'raison_sociale',
        'nom_responsable',
        'statut_juridique',
        'rccm',
        'contact',
        'site_web',
        'reference_appel',
        'offre_technique_path',
        'offre_financiere_path',
        'justificatif_paiement_path',
        'references_path',
        'declaration_honneur',
        'statut',
        'notes_admin'
    ];

    protected $casts = [
        'declaration_honneur' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeAcceptees($query)
    {
        return $query->where('statut', 'acceptee');
    }

    public function scopeRefusees($query)
    {
        return $query->where('statut', 'refusee');
    }

    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'acceptee' => 'Acceptée',
            'refusee' => 'Refusée',
            default => 'Inconnu'
        };
    }

    public function getStatutBadgeClassAttribute()
    {
        return match($this->statut) {
            'en_attente' => 'bg-warning',
            'acceptee' => 'bg-success',
            'refusee' => 'bg-danger',
            default => 'bg-secondary'
        };
    }
}
