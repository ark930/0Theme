<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\ResetPasswordNotification;
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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['emailConfirmPage', 'emailConfirmWithCode']]);
        $this->middleware('auth', ['only' => ['emailConfirmPage']]);
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
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

    public function emailConfirmPage()
    {
        $user = Auth::user();
        if(!empty($user) && $user->registered) {
            return redirect($this->redirectTo);
        }

        return 'emailConfirmPage';
    }

    public function emailConfirmWithCode($confirm_code)
    {
        $user = User::where('email_confirm_code', $confirm_code)->first();

        if(empty($user)) {
            return 'illegal url';
        }
        else if($user->registered) {
            return 'registered';
        } else {
            $user->registered = true;
            $user->save();

            return 'confirmed';
        }
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        event(new Registered($user = $this->create($request->all())));
        $user->saveRegisterInfo($request->ip());
        $this->guard()->login($user);
        $user->notify(new ResetPasswordNotification());

        return redirect($this->redirectPath());
    }
}
