<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'type',
        'active',
        'level',
        'score',
        'content',
    ];

    public function answers() {

        return $this->hasMany(Answer::class);

    }

}

