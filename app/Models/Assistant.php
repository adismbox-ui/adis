<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assistant extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'formateur_id',
        'bio',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    /**
     * Relation avec le formateur responsable
     */
    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }
}
