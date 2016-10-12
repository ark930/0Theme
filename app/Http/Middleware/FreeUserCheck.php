<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class FreeUserCheck
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();
            if($user->isFreeUser() == true) {
                return redirect('/plan');
            }
        }

        return $next($request);
    }
}