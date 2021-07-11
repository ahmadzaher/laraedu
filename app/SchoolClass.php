<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = [
        'name', 'name_numeric'
    ];

    public function sections() {

        return $this->belongsToMany(SchoolSection::class,'sections_classes');

    }
    public function user()
    {
        return $this->hasOne(User::class);
    }

}
