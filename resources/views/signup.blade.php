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
    <div class="box signup">
        <h3>SIGN UP</h3>
        <div class="inner">
            <form class="form-login" role="form" method="POST" action="{{ url('/register') }}">
                {{ csrf_field() }}
                <input type="email" placeholder="Email Address" name="email" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif

                <input type="text" placeholder="Your Name" name="name" value="{{ old('name') }}" required>
                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif

                <input type="password" placeholder="Password" name="password" required>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif

                <label>
                    <input type="checkbox" name="stay" class="checkbox" required> <span>Agree to <a href="">OTheme Terms</a> </span>
                </label>
                <button type="submit" class="button">NEXT</button>
            </form>
            <a href="{{ url('/login') }}" class="forgot">Have Account?</a>
        </div>
    </div>
</div>
<script src="/lib/less.min.js" type="text/javascript"></script>
</body>
</html>