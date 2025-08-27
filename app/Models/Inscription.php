<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    protected $fillable = [
        'apprenant_id',
        'module_id',
        'session_formation_id',
        'date_inscription',
        'statut',
        'mobile_money',
        'moyen_paiement',
    ];

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function sessionFormation()
    {
        return $this->belongsTo(SessionFormation::class);
    }
}
