<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traffic extends Model
{
    protected $fillable = [
        'user_id',
        'type'
    ];
}
