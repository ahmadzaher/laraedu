<?php

namespace App\Http\Middleware;

use App\Traffic;
use Closure;

class LoginCounter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $traffic = new Traffic([
            'user_id' => auth()->user()->id,
            'type' => 'login'
        ]);
        $traffic->save();
        return $next($request);
    }
}
