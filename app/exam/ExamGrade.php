<?php

namespace App\exam;

use Illuminate\Database\Eloquent\Model;

class ExamGrade extends Model
{
    protected $fillable = [
        'name',
        'point',
        'mark_from',
        'mark_upto',
        'note',
    ];
}
