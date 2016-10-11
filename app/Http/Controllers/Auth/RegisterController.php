<?php

namespace App\Http\Controllers\Auth;

use App\Events\LogEvent;
use App\Notifications\ConfirmRegisterNotification;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
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
//        $this->middleware('guest', ['except' => ['registerConfirmPage', 'registerConfirmWithCode']]);
    }

    public function showRegistrationForm()
    {
        return view('signup');
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

    public function registerConfirmPage()
    {
        $user = Auth::user();
        if(!empty($user) && $user->isRegisterConfirmed()) {
            return redirect($this->redirectTo);
        }

        return view('sendemailsuccess');
    }

    public function registerConfirmWithCode(Request $request, $confirm_code)
    {
        $user = User::where('register_confirm_code', $confirm_code)->first();

        if(empty($user)) {
            return 'illegal url';
        }
        else if(!empty($user['register_at'])) {
            return 'registered';
        } else {
            $user->saveRegisterInfo($request->ip());
            event(new LogEvent($request->ip(), $user, LogEvent::REGISTER_CONFIRM));

            $this->guard()->logout();
            $request->session()->flush();
            $request->session()->regenerate();
            return redirect('/login');
        }
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // trigger event
        $user = $this->create($request->all());
        event(new LogEvent($request->ip(), $user, LogEvent::REGISTER));
        $user->notify(new ConfirmRegisterNotification());

        $this->guard()->login($user);

        return redirect($this->redirectPath());
    }
}
