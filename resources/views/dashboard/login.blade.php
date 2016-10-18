@extends('layouts.main')

@section('content')
    <div class="content page-login box-center">
        <div class="box login">
            <h3>LOGIN</h3>
            <div class="inner">
                <form class="form-login" role="form" method="POST" action="{{ url('/login') }}">
                    {{ csrf_field() }}
                    <input type="email" placeholder="Email Address" name="email" value="{{ old('email') }}" required autofocus>
                    @if ($errors->has('email'))
                        <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif

                    <input type="password" placeholder="Password" name="password" required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif

                    @if(null != $redirect)
                        <input type="hidden" name="redirect" value="{{ $redirect }}">
                    @endif
                    <label>
                        <input type="checkbox" name="remember" class="checkbox"> <span>Stay Logged In</span>
                    </label>
                    <button type="submit" class="button">LOGIN</button><a href="{{ url('/register') }}"  class="button">SIGN UP</a>
                </form>
                <a href="{{ url('/password/reset') }}" class="forgot">Forgot Password?</a>
            </div>
        </div>
    </div>
@endsection