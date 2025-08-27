<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReponseQuestionnaire extends Model
{
    protected $table = 'reponse_questionnaires';
    protected $fillable = [
        'apprenant_id',
        'question_id',
        'reponse',
    ];

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
} 