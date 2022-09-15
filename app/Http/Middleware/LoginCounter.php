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
        $traffic = new Traffic(['type' => 'login']);
        $traffic->save();
        return $next($request);
    }
}
