<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuizMeta extends Model
{
    protected $fillable = [
        'quiz_id',
        'key',
        'content'
    ];

    public $timestamps = false;

    protected $table = 'quiz_metas';

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }
}
