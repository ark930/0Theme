@extends('layouts.main')

@section('content')
    <div class="content page-login box-center">
        <div class="box forgot">
            <h5>Forgot Password？</h5>
            <div class="inner">
                <form class="form-login" role="form" method="POST" action="{{ url('/password/email') }}">
                    {{ csrf_field() }}
                    <input type="email" placeholder="Email Address" name="email">
                    @if ($errors->has('email'))
                        <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif

                    <button type="submit" class="button">SEND</button>
                </form>
            </div>
        </div>
    </div>
@endsection