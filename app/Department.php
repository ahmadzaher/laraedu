<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name', 'capacity'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
