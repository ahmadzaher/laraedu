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
        'category_id',
        'branch_id',
        'subject_id',
        'seller_id',
        'year'
    ];

    public function quiz_metas() {

        return $this->hasMany(QuizMeta::class);

    }

    public function questions() {

        return $this->belongsToMany(Question::class,'quizzes_questions');

    }
}
