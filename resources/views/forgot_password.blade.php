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
<script src="/lib/less.min.js" type="text/javascript"></script>
</body>
</html>