<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Auth;

class FreeUserCheck
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user = Auth::user();
            $userRepository = new UserRepository();
            if($userRepository->isFreeUser($user) == true) {
                return redirect('/plan');
            }
        }

        return $next($request);
    }
}