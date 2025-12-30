<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'utilisateurs';

    protected $fillable = [
        'prenom',
        'nom',
        'sexe',
        'categorie',
        'telephone',
        'email',
        'mot_de_passe',
        'type_compte',
        'actif',
        'email_verified_at',
        'infos_complementaires',
        'verification_token',
    ];

    public function apprenant()
    {
        return $this->hasOne(Apprenant::class);
    }

    public function formateur()
    {
        return $this->hasOne(Formateur::class);
    }

    public function assistant()
    {
        return $this->hasOne(Assistant::class);
    }
}
