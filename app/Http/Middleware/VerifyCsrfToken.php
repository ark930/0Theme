<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');

        $response->headers->setCookie(
            new Cookie(
                'XSRF-TOKEN', $request->session()->token(), Carbon::now()->getTimestamp() + 60 * $config['lifetime'],
                $config['path'], $config['domain'], $config['secure'], false
            )
        );

        if($request->session()->has('forum_token')) {
            $response->headers->setCookie(
                new Cookie(
                    'forum_token', $request->session()->get('forum_token'), Carbon::now()->getTimestamp() + 60 * $config['lifetime'],
                    $config['path'], $config['domain'], $config['secure'], false
                )
            );
        }

        return $response;
    }
}
