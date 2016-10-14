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
                    @if(strtolower($membership) === \App\Models\User::MEMBERSHIP_BASIC)
                        <div class="form-group">
                            <label>Theme</label>
                            <h3>{{ $themeName }} <a href="{{ url('/themes') }}" class="button">Choose a theme</a></h3>
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

                <form method="POST" action="#" class="bottom">
                    {{ csrf_field() }}
                    <input type="hidden" value="{{ $productId }}" name="productId" id="productId" placeholder="Product ID">
                    <input type="hidden" value="{{ $paymentType }}" name="paymentType" id="paymentType" placeholder="Payment type">
                    <button type="submit" id ="payPalButton">Pay on PayPal</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#payPalButton').click(function(e) {
            e.preventDefault();
            $('#payPalButton').prop("disabled", true);

            $.ajax({
                url:'/payment/create',
                data: {
                    productId: $('#productId').val(),
                    paymentType: $('#paymentType').val()
                },
                type:'post',
                dataType:'json',
                success: successHandler,
                error : errorHandler
            });
        });

        function successHandler(data)
        {
            console.log(data);
            window.location.href = data.approveUrl;
        }

        function errorHandler(data)
        {
            $('#payPalButton').prop("disabled", false);
            var error = JSON.parse(data.responseText);
            console.log(error);
            alert(error.error);
        }
    </script>
@endsection
