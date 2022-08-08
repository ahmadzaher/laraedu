<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

class Bank extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function resolveRouteBinding($value, $field = null)
    {
        return self::where('id', $value)->first() ?? abort(Response::HTTP_NOT_FOUND, 'Not Found');
    }

    public function scopeFilter($query, array $filter)
    {
        $query->when($filter['search'] ?? false, function ($query, $search) {
            $query
                ->where('name', 'like', "%$search%")
                ->orWhere('banks.id', 'like', "%$search%");
        });
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class,'banks_questions');
    }
}
