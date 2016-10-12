<html>
<head>
    <title>Zero Admin</title>
    <link rel="stylesheet/less" type="text/css" href="{{ asset('/lib/style.less') }}"/>
    @yield('head')
</head>
<body>
    <header>
        <div class="logo">
            <img src="{{ asset('/img/logo.png') }}"/>THEME.<span>com</span>
        </div>
        <div class="menu">
            <a >Home</a>
            <a >Themes</a>
            <a >ZEN</a>
            <a >Pricing</a>
            <a >Forum</a>
        </div>
        <div class="menu tool">
            <a href="{{ url('/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                Logout
            </a>
            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </div>
    </header>

    @yield('content')

    <script src="{{ asset('/lib/less.min.js') }}" type="text/javascript"></script>
    @if($errors->count() > 0)
        <script>
            window.onload = function() {
                alert("{!! $errors->first() !!}");
            };
        </script>
    @endif

    @yield('footer')
</body>
</html>