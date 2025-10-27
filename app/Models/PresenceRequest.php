<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresenceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'formateur_id',
        'nom',
        'commentaire',
        'is_open',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    public function formateur()
    {
        return $this->belongsTo(Formateur::class);
    }

    public function marks()
    {
        return $this->hasMany(PresenceMark::class);
    }
}

