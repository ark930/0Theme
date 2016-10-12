@extends('layouts.main')

@section('content')
    <div class="content page-order box-center">
        <div class="box order">
            <div class="left">
                <div class="top">
                    <h1>{{ strtoupper($membership) }}</h1>
                    <p>One Premium Theme</p>
                </div>
                <div class="price-list">
                    <ul>
                        <li>ONE WordPress theme</li>
                        <li> 1 year of theme updates</li>
                        <li> 1 year of personal support</li>
                        <li>  Includes The ZEN Panel</li>
                        <li>  One-Time Purchase Fee</li>
                    </ul>
                </div>
                @if(null !== $upgrade)
                    <a href="{{ $upgrade['link'] }}" class="ad">
                        <h3>{{ strtoupper($upgrade['membership']) }} <span>${{ $upgrade['price'] }}</span></h3>
                        <p>All Premium Theme & Plugins<br/>Zen Package</p>
                    </a>
                @endif
            </div>
            <div class="right">
                <div class="top">
                    Total Price: <span>{{ $price ? '$' . $price : ''}}</span>
                </div>
                <div class="mid">
                    <div class="form-group">
                        <label>Membership</label>
                        <h3>{{ ucfirst(strtolower($membership)) }}</h3>
                    </div>
                    @if($membership == \App\Models\User::MEMBERSHIP_BASIC)
                        <div class="form-group">
                            <label>Theme</label>
                            <h3>{{ $themeName }} <a href="{{ url('/themes') }}" class="button">Choose Another</a></h3>
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
                </div>

                <form method="POST" action="{{ url('/payment/create') }}" class="bottom">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $productId }}" name="product_id" placeholder="Product ID">
                    <input type="hidden" value="{{ $paymentType }}" name="payment_type" placeholder="Payment type">
                    <button type="submit">Pay on PayPal</button>
                </form>
            </div>
        </div>
    </div>
@endsection
