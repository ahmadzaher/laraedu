<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Summary extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $guarded = [];

     public function resolveRouteBinding($value, $field = null)
    {
        return self::where('id', $value)->first() ?? abort(404, 'Not Found');
    }


    public function registerMediaCollections()
    {
        $this->addMediaCollection('summaries')
            ->acceptsMimeTypes(['application/pdf', 'application/msword']);
    }
}
