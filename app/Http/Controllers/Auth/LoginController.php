<?php

namespace App\Http\Controllers\Auth;

use App\Events\LogEvent;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
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

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

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
        $redirect = $request->input('redirect');

        return view('dashboard.login', compact('redirect'));
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
            ->withCookie(Cookie::forget(env('FORUM_TOKEN_NAME'), config('session.path'), env('FORUM_DOMAIN')));
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
        $this->userRepository->saveLoginInfo($user, $request->ip());
        event(new LogEvent($request->ip(), $this->guard()->user(), LogEvent::LOGIN));

        if($request->has('redirect')) {
            $this->redirectTo = $request->input('redirect');
        } else if($this->userRepository->isRegisterConfirmed($user) == false) {
            redirect()->intended('/register/confirm');
        } else if($this->userRepository->isFreeUser($user) == true) {
            $this->redirectTo = '/plan';
        }

        $token = $user->createToken('access_token')->accessToken;
        $request->session()->put(env('FORUM_TOKEN_NAME'), $token);
        $config = config('session');

        return redirect()->intended($this->redirectPath())
            ->withCookie(env('FORUM_TOKEN_NAME'), $token, $config['lifetime'], $config['path'], env('FORUM_DOMAIN'), $config['secure'], false);
    }
}
