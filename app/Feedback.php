<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Feedback extends Model
{
    protected $fillable = [
        'user_id',
        'text'
    ];
}
