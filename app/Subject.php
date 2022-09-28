<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name', 'description', 'branch_id', 'year',
    ];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function summaries()
    {
        return $this->hasMany(Summary::class);
    }
}
