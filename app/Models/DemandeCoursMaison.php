<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeCoursMaison extends Model
{
    use HasFactory;
    
    protected $table = 'demandes_cours_maison';
    
    // Constantes pour les statuts
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_VALIDEE = 'validee';
    const STATUT_EN_ATTENTE_FORMATEUR = 'en_attente_formateur';
    const STATUT_REFUSEE = 'refusee';
    const STATUT_ACCEPTEE_FORMATEUR = 'acceptee_formateur';
    const STATUT_REFUSEE_FORMATEUR = 'refusee_formateur';
    
    protected $fillable = [
        'user_id', 'niveau_id', 'module', 'nombre_enfants', 'ville', 'commune', 'quartier', 'numero', 'formateur_id', 'statut', 'message'
    ];

    /**
     * Obtenir tous les statuts valides
     */
    public static function getStatutsValides()
    {
        return [
            self::STATUT_EN_ATTENTE,
            self::STATUT_VALIDEE,
            self::STATUT_EN_ATTENTE_FORMATEUR,
            self::STATUT_REFUSEE,
            self::STATUT_ACCEPTEE_FORMATEUR,
            self::STATUT_REFUSEE_FORMATEUR,
        ];
    }

    /**
     * Vérifier si un statut est valide
     */
    public static function isStatutValide($statut)
    {
        return in_array($statut, self::getStatutsValides());
    }

    public function user()
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    public function niveau()
    {
        return $this->belongsTo(\App\Models\Niveau::class, 'niveau_id');
    }

    public function module()
    {
        return $this->belongsTo(\App\Models\Module::class, 'module_id');
    }

    public function formateur()
    {
        return $this->belongsTo(\App\Models\Formateur::class, 'formateur_id');
    }
}
