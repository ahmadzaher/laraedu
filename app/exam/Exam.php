<?php

namespace App\exam;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['name', 'date', 'note'];
}
