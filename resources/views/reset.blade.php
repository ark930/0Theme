<html>
<head>
    <title>Zero Theme</title>
    <link rel="stylesheet/less" type="text/css" href="/lib/style.less" />
</head>
<body>
<header>
    <header>
        <div class="logo">
            <img src="/img/logo.png"/>THEME.<span>com</span>
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
<script src="/lib/less.min.js" type="text/javascript"></script>
</body>
</html>