<?php

namespace App\Http\Middleware;

use Closure;

class FrontendActiveMiddleware
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
        if(!option('active')) {

            abort(404);

        }

        return $next($request);
    }
}
