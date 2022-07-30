<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Answer extends Model  implements HasMedia
{
    use HasMediaTrait;

    protected $fillable = [
        'question_id',
        'active',
        'correct',
        'content',
    ];

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('answer_images')
            ->width(150)
            ->height(150);
    }
}
