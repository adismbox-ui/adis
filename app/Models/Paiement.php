<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'apprenant_id',
        'module_id',
        'montant',
        'date_paiement',
        'statut',
        'methode',
        'reference',
        'notes',
        'informations_paiement',
    ];

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Récupère les informations de paiement décodées.
     */
    public function getInformationsPaiementDecodeesAttribute()
    {
        return $this->informations_paiement ?? [];
    }
}
