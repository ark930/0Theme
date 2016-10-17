<?php

namespace App\Http\Controllers\Auth;

use App\Events\LogEvent;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Login page
     *
     * Route: GET /login
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        $target = $request->input('target');

        return view('dashboard.login', compact('target'));
    }

    /**
     * Log the user out of the application.
     *
     * Route: POST /logout
     *
     * @param  Request  $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        event(new LogEvent($request->ip(), $this->guard()->user(), LogEvent::LOGOUT, 'I\'m leaving now.'));
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect('/login')
            ->withCookie(Cookie::forget('forum_token', config('session.path'), config('session.domain')));
    }

    /**
     * The user has been authenticated.
     * This function will be called when login success
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user->saveLoginInfo($request->ip());
        event(new LogEvent($request->ip(), $this->guard()->user(), LogEvent::LOGIN));

        if($request->has('target') && strtolower($request->input('target')) == 'forum') {
            $this->redirectTo = 'http://forum.theme.com:8001';
        } else if($user->isRegisterConfirmed() == false) {
            redirect()->intended('/register/confirm');
        } else if($user->isFreeUser() == true) {
            $this->redirectTo = '/plan';
        }

        $token = $user->createToken('access_token')->accessToken;
        $request->session()->put('forum_token', $token);
        $config = config('session');

        return redirect()->intended($this->redirectPath())
            ->withCookie('forum_token', $token, $config['lifetime'], $config['path'], $config['domain'], $config['secure'], false);
    }
}
