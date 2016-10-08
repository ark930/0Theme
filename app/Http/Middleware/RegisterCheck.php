<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RegisterCheck
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();
            if($user->isRegistered() == false) {
                return redirect('/register/confirm');
            }
        }

        return $next($request);
    }
}