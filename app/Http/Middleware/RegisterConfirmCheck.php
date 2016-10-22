<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Auth;

class RegisterConfirmCheck
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();
            $userRepository = new UserRepository();
            if($userRepository->isRegisterConfirmed($user) == false) {
                return redirect('/register/confirm');
            }
        }

        return $next($request);
    }
}