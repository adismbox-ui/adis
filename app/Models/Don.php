<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Don extends Model
{
    protected $fillable = [
        'nom_donateur',
        'email_donateur',
        'telephone',
        'montant',
        'type_don',
        'projet_id',
        'mode_paiement',
        'recu_demande',
        'message',
        'statut',
        'date_don',
        'numero_reference',
        'transaction_id',
        'paiement_confirme',
        'date_confirmation',
        'notes_admin'
    ];

    protected $casts = [
        'date_don' => 'datetime',
        'date_confirmation' => 'datetime',
        'recu_demande' => 'boolean',
        'paiement_confirme' => 'boolean',
        'montant' => 'decimal:2'
    ];

    // Relations
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    // Accesseurs
    public function getStatutLabelAttribute()
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'confirme' => 'Confirmé',
            'annule' => 'Annulé',
            'refuse' => 'Refusé',
            default => $this->statut
        };
    }

    public function getStatutColorAttribute()
    {
        return match($this->statut) {
            'en_attente' => 'warning',
            'confirme' => 'success',
            'annule' => 'danger',
            'refuse' => 'secondary',
            default => 'secondary'
        };
    }

    public function getTypeDonLabelAttribute()
    {
        return match($this->type_don) {
            'ponctuel' => 'Ponctuel',
            'mensuel' => 'Mensuel',
            default => $this->type_don
        };
    }

    public function getModePaiementLabelAttribute()
    {
        return match($this->mode_paiement) {
            'carte' => 'Carte bancaire',
            'virement' => 'Virement bancaire',
            'mobile' => 'Mobile money',
            default => $this->mode_paiement
        };
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeConfirmes($query)
    {
        return $query->where('statut', 'confirme');
    }

    public function scopeAnnules($query)
    {
        return $query->where('statut', 'annule');
    }

    public function scopePonctuels($query)
    {
        return $query->where('type_don', 'ponctuel');
    }

    public function scopeMensuels($query)
    {
        return $query->where('type_don', 'mensuel');
    }
}
