<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresenceMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'presence_request_id',
        'apprenant_id',
        'present_at',
    ];

    protected $casts = [
        'present_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(PresenceRequest::class, 'presence_request_id');
    }

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }
}

