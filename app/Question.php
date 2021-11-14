<?php

namespace App;

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
        'content',
        'attachment_type',
        'attachment_url'
    ];

    use HasMediaTrait;

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('question_image')
            ->width(800)
            ->height(800);
    }

    public function answers() {

        return $this->hasMany(Answer::class);

    }

}

