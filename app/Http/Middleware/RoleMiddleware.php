<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{

    public function handle($request, Closure $next, $role, $permission = null)
    {
        $roles = explode('|', $role);
        $has_role = false;
        foreach ($roles as $role)
            if($request->user()->hasRole($role)) {

                $has_role = true;

            }
        if(!$has_role)
            abort(403);

        if($permission !== null && !$request->user()->can($permission)) {

            abort(403);
        }

        return $next($request);

    }
}
