<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacance extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'date_debut',
        'date_fin',
        'actif'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'actif' => 'boolean',
    ];

    public function scopeActives($query)
    {
        return $query->where('actif', true);
    }

    public function scopePourPeriode($query, $dateDebut, $dateFin)
    {
        return $query->where(function ($q) use ($dateDebut, $dateFin) {
            $q->whereBetween('date_debut', [$dateDebut, $dateFin])
              ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
              ->orWhere(function ($subQ) use ($dateDebut, $dateFin) {
                  $subQ->where('date_debut', '<=', $dateDebut)
                       ->where('date_fin', '>=', $dateFin);
              });
        });
    }
} 