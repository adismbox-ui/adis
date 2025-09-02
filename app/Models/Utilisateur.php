<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Utilisateur extends Authenticatable implements JWTSubject
{
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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

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
