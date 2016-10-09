<html>
<head>
    <title>Zero Theme</title>
    <link rel="stylesheet/less" type="text/css" href="{{ asset('lib/style.less') }}" />
</head>
<body>
<header>
    <header>
        <div class="logo">
            <img src="{{ asset('img/logo.png') }}"/>THEME.<span>com</span>
        </div>
        <div class="menu">
            <a >Home</a>
            <a >Themes</a>
            <a >ZEN</a>
            <a >Pricing</a>
            <a >Forum</a>
        </div>
        <div class="menu tool">
            <a href="">Join</a>
        </div>
    </header>
</header>
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

                <label>
                    <input type="checkbox" name="remember" class="checkbox"> <span>Stay Logged In</span>
                </label>
                <button type="submit" class="button">LOGIN</button><a href="{{ url('/register') }}"  class="button">SIGN UP</a>
            </form>
            <a href="{{ url('/password/reset') }}" class="forgot">Forgot Password?</a>
        </div>
    </div>
</div>
<script src="{{ asset('lib/less.min.js') }}" type="text/javascript"></script>
</body>
</html>