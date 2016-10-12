<html>
<head>
    <title>Zero Admin</title>
    <link rel="stylesheet/less" type="text/css" href="/lib/style.less"/>
    @yield('head')
</head>
<body>
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
            <a href="{{ url('/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                Logout
            </a>
            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </div>
    </header>

    @yield('content')

    @yield('footer')
</body>
</html>