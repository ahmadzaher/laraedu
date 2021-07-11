<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolSection extends Model
{
    protected $fillable = [
        'name', 'capacity'
    ];

    public function classes() {

        return $this->belongsToMany(SchoolClass::class,'sections_classes');

    }
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
