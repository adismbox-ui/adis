<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificat extends Model
{
    protected $fillable = [
        'apprenant_id',
        'module_id',
        'titre',
        'date_obtention',
        'fichier',
    ];

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
