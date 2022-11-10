<?php

namespace App\Http\Middleware;

use Closure;

class BranchMiddleware
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
        $user = auth()->user();
        if($user->hasRole('staff')){
            $_REQUEST['branch_id'] = $user->branch_id;
            $_REQUEST['seller_id'] = $user->seller_id;
        }elseif($user->hasRole('teacher')){
            $_REQUEST['branch_id'] = $user->branch_id;
            $_REQUEST['year'] = $user->year;
            $_REQUEST['seller_id'] = $user->seller_id;
            $_REQUEST['subject_id'] = $user->subject_id;
        }
        return $next($request);
    }
}
