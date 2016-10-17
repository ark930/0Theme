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
                if ($request->has('target') && strtolower($request->input( 'target' )) == 'forum') {
                    $this->redirectTo = 'http://forum.theme.com:8001';

                    $token = Auth::user()->createToken('access_token')->accessToken;
                    return redirect('http://forum.theme.com:8001')
                        ->withCookie('forum_token', $token, config('session.lifetime'), config('session.path'), '.theme.com');

                }
            }
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
