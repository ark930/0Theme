<form method="POST" action="/payment/create">
    {{ csrf_field() }}
    <input type="hidden" value="1" name="product_id" placeholder="Product ID">
    <input type="hidden" value="paypal" name="payment_type" placeholder="Payment type">
    <button type="submit">Basic</button>
</form>

<form method="POST" action="/payment/create">
    {{ csrf_field() }}
    <input type="hidden" value="2" name="product_id" placeholder="Product ID">
    <input type="hidden" value="paypal" name="payment_type" placeholder="Payment type">
    <button type="submit">Pro</button>
</form>

<form method="POST" action="/payment/create">
    {{ csrf_field() }}
    <input type="hidden" value="3" name="product_id" placeholder="Product ID">
    <input type="hidden" value="paypal" name="payment_type" placeholder="Payment type">
    <button type="submit">Lifetime</button>
</form>

@if($errors->count() > 0)
    <script>
        window.onload = function() {
            alert("{!! $errors->first() !!}");
        };
    </script>
@endif