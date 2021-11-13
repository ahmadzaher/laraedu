<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'meta_title',
        'slug',
        'summary',
        'type',
        'score',
        'published',
        'starts_at',
        'ends_at',
        'content',
    ];

    public function quiz_metas() {

        return $this->hasMany(QuizMeta::class);

    }
    public function questions() {

        return $this->hasMany(Question::class);

    }
    public function answers() {

        return $this->hasMany(Answer::class);

    }
}
