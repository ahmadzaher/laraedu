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
}
