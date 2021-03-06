@extends('layouts.main')

@section('head')
    <link rel="stylesheet" href="{{ asset('/lib/tagator.jquery.css') }}"/>
@endsection

@section('content')
    @inject('userRepository', 'App\Repositories\UserRepository')
    <div class="content page-user">
        <div class="left">
            <div class="top">
                {{ $user['name'] }}<span class="member {{ $user['membership'] }}">{{ ucfirst($user['membership']) }}</span>
            </div>
            <div class="inner">
                <div class="form-group">
                    <label>Email</label>
                    <h3>{{ $user['email'] }}</h3>
                </div>
                <div class="form-group">
                    <label>Registion Time</label>
                    <h3>{{ $user['register_at'] }}</h3>
                </div>
                <div class="form-group">
                    <label>Lastest Login</label>
                    <h3>{{ $user['last_login_at'] }}</h3>
                </div>
                <hr/>
                <div class="form-group">
                    <label>Member</label>
                    <h3>{{ ucfirst($user['membership']) }}</h3>
                </div>

                @if($user['membership'] == \App\Models\User::MEMBERSHIP_PRO)
                    <div class="form-group">
                        <label>Start with</label>
                        <h3>{{ $user['pro_from'] }}</h3>
                    </div>
                    <div class="form-group">
                        <label>Expired by</label>
                        <h3>{{ $user['pro_to'] }}</h3>
                    </div>
                @endif
                @if($userRepository->isFreeUser($user))
                    <div class="form-group link">
                        <a href="{{ url('/plan') }}" class="button line">Choose A Plan</a>
                    </div>
                @else
                    @if($userRepository->isBasicUser($user))
                        <div class="form-group link">
                            <!--renew 在basic上是没有的-->
                            <!--<a href="#" class="button">Renew</a>-->
                            <!--注释要删除-->
                            <!--文案逻辑为:-->
                            <!--1.如果是basic,并在一个月内,补足差价升级pro,注意这里的差价是pro当前折扣价格（销售价格） 减去 basic 购买时的价格。-->
                            <a href="{{ url('/plan/pro') }}" class="button line">Upgrade to Pro ($30）</a>
                            <label>27days left</label>
                            <!--2.如果超过一个月了,需要全额升级-->
                            <!--<a href="#" class="button">Buy Pro（$149）</a>-->
                            <!--3.如果是Pro用户,可以永久差价,升级Lifetime,Lifetime终生不打折-->
                            <!--<a href="#" class="button">Upgrade to Lifetime ($79）</a>-->
                        </div>
                    @endif

                    @if(!$userRepository->isLifetimeUser($user))
                        <div class="form-group link">
                            <a href="{{ url('/plan/lifetime') }}" class="button line">Upgrade to Lifetime ($249）</a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
        <div class="right">
            <ul class="tab">
                <li class="cur">MyThemes</li>
                <li>Forum</li>
                <li>Payment</li>
            </ul>
            <div class="user-themes tab-content on">
                @if($user['membership'] ==  \App\Models\User::MEMBERSHIP_BASIC)
                    <table>
                        <tbody>
                        @foreach($themes as $theme)
                            <tr>
                                <td><label>Theme</label>{{ $theme['name'] }}</td>
                                <td><label>Version</label>{{ $theme->currentVersion['version'] }}</td>
                                <td><label>Release Date</label>{{ $theme->currentVersion['release_at'] }}</td>
                                <td><label>Expired Date</label>{{ date('M d, Y', strtotime($theme->pivot['basic_to'])) }}</td>
                                <td>
                                    <a class="button">Download</a>
                                    <a class="button">Document</a>
                                    <a class="button">Forum</a>
                                    <a class="button">Renew</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @elseif($userRepository->isAdvanceUser($user))
                    <table>
                        <tbody>
                        @foreach($themes as $theme)
                            <tr>
                                <td><label>Theme</label>{{ $theme['name'] }}</td>
                                <td><label>Version</label>{{ $theme->currentVersion['version'] }}</td>
                                <td><label>Version</label>{{ $theme->currentVersion['release_at'] }}</td>
                                <td>
                                    <a class="button">Download</a>
                                    <a class="button">Document</a>
                                    <a class="button">Forum</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="user-forum tab-content">
                <ul>
                    <li>
                        <a href="">How to config the zen panel? <span class="status">Open</span><br/><time>9 Jan 2016</time> </a>
                    </li>
                    <li>
                        <a href="">How to config the zen panel? <span class="status closed">Closed</span><br/><time>9 Jan 2016</time> </a>
                    </li>
                    <li>
                        <a href="">How to config the zen panel? <span class="status closed">Closed</span><br/><time>9 Jan 2016</time> </a>
                    </li>
                    <li>
                        <a href="">How to config the zen panel? <span class="status closed">Closed</span><br/><time>9 Jan 2016</time> </a>
                    </li><li>
                        <a href="">How to config the zen panel? <span class="status closed">Closed</span><br/><time>9 Jan 2016</time> </a>
                    </li><li>
                        <a href="">How to config the zen panel? <span class="status closed">Closed</span><br/><time>9 Jan 2016</time> </a>
                    </li>
                    <li>
                        <a href="">How to config the zen panel? <span class="status closed">Closed</span><br/><time>9 Jan 2016</time> </a>
                    </li>
                </ul>
            </div>
            <div class="user-payment tab-content">
                <table border="0">
                    <thead>
                    <tr>
                        <th>Product</th>
                        <th>Account</th>
                        <th>No.</th>
                        <th>Created Date</th>
                        <th>Amount</th>
                        <th>Status</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <?php $product = $order->product ?>
                        <tr>
                            @if($product['type'] == \App\Models\Product::TYPE_THEME)
                                <td>Basic<span>{{ $order['name'] }}</span></td>
                            @else
                                <td>{{ $order['name'] }}</td>
                            @endif
                            <td>{{ $order->currentPayPalPayment['payer_email'] }}<span>{{ $order['payment_type'] }}</span></td>
                            <td>{{ $order['order_no'] }}</td>
                            <td>{{ $order['created_at'] }}</td>
                            <td>+${{ $order['price'] }}</td>
                            <td>{{ ucfirst($order['status']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
//        var i = $(".ip").html();
//        $.get("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js", { ip:i }, function(data){
//            alert(data);
//        });
            $(".tab li").click(function(){
                $(".tab li").eq($(this).index()).addClass("cur").siblings().removeClass('cur');
                $(".tab-content").hide().eq($(this).index()).show();
                //另一种方法: $("div").eq($(".tab li").index(this)).addClass("on").siblings().removeClass('on');

            });
        });
    </script>
@endsection