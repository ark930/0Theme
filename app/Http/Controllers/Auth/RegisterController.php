<?php

namespace App\Http\Controllers\Auth;

use App\Events\LogEvent;
use App\Models\ForumUser;
use App\Notifications\ConfirmRegisterNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['registerConfirmPage', 'registerConfirmWithCode']]);
    }

    /**
     * Register page
     *
     * Route: GET /register
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('dashboard.signup');
    }

    /**
     * Register request
     *
     * Route: POST /register
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(Request $request)
    {
        // validate parameters
        $this->validator($request->all())->validate();

        // create user
        $user = $this->create($request->all());
        
        // create forum user
        ForumUser::newUser($user['name'], $user['email'], $user['created_at']);

        // login
        $this->guard()->login($user);

        // Send confirm register email to user
        $user->notify(new ConfirmRegisterNotification());

        event(new LogEvent($request->ip(), $user, LogEvent::REGISTER));

        return redirect()->route('register_confirm');
    }

    /**
     * Register confirm page
     *
     * Route: GET /register/confirm
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function registerConfirmPage()
    {
        $user = Auth::user();
        if(!empty($user) && $user->isRegisterConfirmed()) {
            return redirect($this->redirectTo);
        }

        return view('dashboard.sendemailsuccess');
    }

    /**
     * Register confirm from email
     *
     * Route: GET /register/confirm/{confirm_code}
     *
     * @param Request $request
     * @param $confirm_code
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function registerConfirmWithCode(Request $request, $confirm_code)
    {
        $user = Auth::user();
        if(!empty($user) && $user->isRegisterConfirmed()) {
            return redirect($this->redirectTo);
        }

        $user = User::where('register_confirm_code', $confirm_code)->first();

        if(empty($user)) {
            return view('errors.message', ['error' => 'This url is expired or invalid.']);
        } else if(!empty($user['register_at'])) {
            return view('errors.message', ['error' => 'This url is expired or invalid.']);
        } else {
            $user->saveRegisterInfo($request->ip());
            event(new LogEvent($request->ip(), $user, LogEvent::REGISTER_CONFIRM));

            // logout
            $this->guard()->logout();
            $request->session()->flush();
            $request->session()->regenerate();

            return view('dashboard.register_done');
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::newUser($data);
    }
}
