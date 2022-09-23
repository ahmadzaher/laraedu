<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'title',
        'summary',
        'type',
        'score',
        'published',
        'content',
        'category_id',
        'branch_id',
        'subject_id',
        'seller_id',
        'year',
        'price'
    ];

    public function questions() {

        return $this->belongsToMany(Question::class,'quizzes_questions');

    }
}
