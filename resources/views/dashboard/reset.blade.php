@extends('layouts.main')

@section('content')
    <div class="content page-login box-center">
        <div class="box forgot">
            <h5>Reset Password</h5>
            <div class="inner">
                <form class="form-login" role="form" method="POST" action="{{ url('/password/reset') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="token" value="{{ $token }}">

                    <input type="hidden" name="email" value="{{ $email }}">
                    @if ($errors->has('email'))
                        <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif

                    <input type="password" placeholder="New Password" name="password" required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif

                    <input type="password" placeholder="Repeat Password" name="password_confirmation" required>
                    @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                    @endif

                    <button type="submit" class="button">SAVE</button>
                </form>
            </div>
        </div>
    </div>
@endsection