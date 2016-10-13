<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RegisterConfirmCheck
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();
            if($user->isRegisterConfirmed() == false) {
                return redirect('/register/confirm');
            }
        }

        return $next($request);
    }
}