<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if($request->path() == 'login') {
                if($request->has('redirect')) {
                    $token = Auth::user()->createToken('access_token')->accessToken;

                    return redirect($request->input('redirect'))
                        ->withCookie(env('FORUM_TOKEN_NAME'), $token, config('session.lifetime'), config('session.path'), env('FORUM_DOMAIN'));
                }
            }

            return redirect('/dashboard');
        }

        return $next($request);
    }
}
