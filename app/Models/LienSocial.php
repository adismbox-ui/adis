<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LienSocial extends Model
{
    use HasFactory;

    protected $table = 'liens_sociaux';

    protected $fillable = [
        'nom',
        'titre',
        'description',
        'url',
        'icone',
        'couleur',
        'actif',
        'ordre'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer'
    ];

    /**
     * Scope pour récupérer uniquement les liens actifs
     */
    public function scopeLiensActifs($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Méthode pour activer/désactiver un lien social
     */
    public function toggleActif()
    {
        $this->actif = !$this->actif;
        $this->save();
        return $this;
    }

    /**
     * Méthode pour changer l'ordre d'un lien social
     */
    public function changerOrdre($nouvelOrdre)
    {
        $this->ordre = $nouvelOrdre;
        $this->save();
        return $this;
    }
}
