<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppelAProjet extends Model
{
    protected $fillable = [
        'reference',
        'intitule',
        'domaine',
        'date_limite_soumission',
        'etat',
        'details_offre',
        'montant_estimatif',
        'beneficiaires',
        'partenaire_retenu',
        'date_cloture'
    ];

    protected $casts = [
        'date_limite_soumission' => 'date',
        'date_cloture' => 'date',
        'montant_estimatif' => 'decimal:2'
    ];

    public function scopeEnCours($query)
    {
        return $query->where('etat', 'ouvert');
    }

    public function scopeClotures($query)
    {
        return $query->where('etat', 'cloture');
    }
}
