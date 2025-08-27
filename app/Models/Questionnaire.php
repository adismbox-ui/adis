<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    protected $fillable = [
        'titre', 
        'description', 
        'module_id', 
        'niveau_id',
        'session_id',
        'date_envoi',
        'envoye',
        'minutes', 
        'semaine', 
        'type_devoir', 
        'user_id'
    ];
    
    protected $with = ['module.niveau'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }
    
    public function session()
    {
        return $this->belongsTo(SessionFormation::class, 'session_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
