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

<div class="content page-plans">
    <div class="plans">
        <div class="plan basic">
            <div class="inner">
                <div class="top">
                    <h1>Basic</h1>
                    <p>One Premium Theme</p>
                </div>
                <div class="price">
                    $<span>{{ $basicProduct['price'] }}</span>/year
                </div>
                <div class="price-list">
                    <ul>
                        <li>ONE WordPress theme</li>
                        <li> 1 year of theme updates</li>
                        <li> 1 year of personal support</li>
                        <li>  Includes The ZEN Panel</li>
                        <li>  One-Time Purchase Fee</li>
                    </ul>
                    <ul>
                        <li class="disable">ONE WordPress theme</li>
                        <li class="disable"> 1 year of theme updates</li>
                        <li class="disable"> 1 year of personal support</li>
                        <li class="disable">  Includes The ZEN Panel</li>
                        <li class="disable">  One-Time Purchase Fee</li>
                    </ul>
                </div>
                <form class="buy" method="GET" action="/plan/basic">
                    <button >Choose A Theme</button>
                </form>
            </div>
        </div>
        <div class="plan pro">
            <div class="inner">
                <div class="top">
                    <h1>Pro</h1>
                    <p>All Premium Theme & Plugins</p>
                </div>
                <div class="price">
                    $<span>{{ $proProduct['price'] }}</span>/year
                </div>
                <div class="price-list">
                    <ul>
                        <li>ONE WordPress theme</li>
                        <li> 1 year of theme updates</li>
                        <li> 1 year of personal support</li>
                        <li>  Includes The ZEN Panel</li>
                        <li>  One-Time Purchase Fee</li>
                    </ul>
                    <ul>
                        <li>ONE WordPress theme</li>
                        <li> 1 year of theme updates</li>
                        <li> 1 year of personal support</li>
                        <li>  Includes The ZEN Panel</li>
                        <li>  One-Time Purchase Fee</li>
                    </ul>
                </div>
                <form class="buy" method="GET" action="/plan/pro">
                    <button>Buy Pro</button>
                </form>
            </div>
        </div>
        <div class="plan lifetime ">
            <div class="inner">
                <div class="top">
                    <h1>Lifetime</h1>
                    <p>One-Time Purchase Fee</p>
                </div>
                <div class="price">
                    $<span>{{ $lifetimeProduct['price'] }}</span>
                </div>
                <div class="price-list">
                    <ul>
                        <li>ONE WordPress theme</li>
                        <li> 1 year of theme updates</li>
                        <li> 1 year of personal support</li>
                        <li>  Includes The ZEN Panel</li>
                        <li>  One-Time Purchase Fee</li>
                    </ul>
                    <ul>
                        <li>ONE WordPress theme</li>
                        <li> 1 year of theme updates</li>
                        <li> 1 year of personal support</li>
                        <li>  Includes The ZEN Panel</li>
                        <li>  One-Time Purchase Fee</li>
                    </ul>
                </div>
                <form class="buy" method="GET" action="/plan/lifetime">
                    <button >Buy Lifetime</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="/lib/less.min.js" type="text/javascript"></script>
@if($errors->count() > 0)
    <script>
        window.onload = function() {
            alert("{!! $errors->first() !!}");
        };
    </script>
@endif
</body>
</html>


