<html>
<head>
    <title>Zero Theme</title>
    <link rel="stylesheet/less" type="text/css" href="/lib/style.less" />
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
        <a href="">Join</a>
    </div>
</header>

<div class="content page-login page-reciept box-center">
    <div class="box reciept">
        <h3>Payment Success</h3>
        <div class="inner">
            <div class="form-group">
                <label>Membership</label>
                <h3>{{ ucfirst($membership) }}</h3>
            </div>
            @if($membership == \App\Models\User::MEMBERSHIP_BASIC)
            <div class="form-group">
                <label>Theme</label>
                <h3>{{ $themeName }}</h3>
            </div>
            @endif
            <div class="form-group">
                <label>Period</label>
                <h3>{{ $period }}</h3>
            </div>
            <div class="form-group">
                <label>Account</label>
                <h3>{{ $account }}</h3>
            </div>
            <div class="form-group ammout">
                <label>Total</label>
                <h3>${{ $total }}</h3>
            </div>
            <div class="form-group link">
                <a class="button" href="{{ url('/dashboard') }}">Dashboard</a>
            </div>
        </div>
    </div>
</div>
<script src="/lib/less.min.js" type="text/javascript"></script>
</body>
</html>