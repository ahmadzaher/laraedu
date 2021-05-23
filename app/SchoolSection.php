<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolSection extends Model
{
    protected $fillable = [
        'name', 'capacity'
    ];

    public function sections() {

        return $this->belongsToMany(SchoolClass::class,'sections_classes');

    }
}
