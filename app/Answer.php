<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'quiz_id',
        'question_id',
        'active',
        'correct',
        'content',
    ];
}
