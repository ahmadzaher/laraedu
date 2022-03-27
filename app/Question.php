<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Question extends Model implements HasMedia
{
    protected $fillable = [
        'type',
        'active',
        'level',
        'score',
        'default_time',
        'content',
        'attachment_type',
        'attachment_url',
        'group_id',
        'solution',
        'hint'
    ];

    use HasMediaTrait;

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('question_image')
            ->width(100)
            ->height(100);
    }

    public function answers() {

        return $this->hasMany(Answer::class);

    }

    public function group()
    {
        return $this->belongsTo(QuestionGroup::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function quizzes() {

        return $this->belongsToMany(Quiz::class,'quizzes_questions');

    }

}

