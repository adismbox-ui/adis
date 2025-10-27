<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['questionnaire_id', 'texte', 'choix', 'bonne_reponse', 'points'];

    protected $casts = [
        'choix' => 'array',
        'points' => 'integer',
    ];

    /**
     * Accesseur pour s'assurer que les choix sont toujours un tableau valide
     */
    public function getChoixAttribute($value)
    {
        // Si c'est déjà un tableau, le retourner
        if (is_array($value)) {
            return $value;
        }
        
        // Si c'est une chaîne JSON, essayer de la décoder
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            
            // Essayer de séparer par des points-virgules
            $choices = array_map('trim', explode(';', $value));
            $choices = array_filter($choices, function($choice) {
                return !empty($choice);
            });
            
            if (count($choices) >= 2) {
                return array_values($choices);
            }
        }
        
        // Retourner un tableau vide par défaut
        return [];
    }

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
