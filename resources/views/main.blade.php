<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>
<body>
    <h2>User</h2>
    <p><strong>Username</strong>: {{ $user['name'] }}</p>
    <p><strong>Email</strong>: {{ $user['email'] }}</p>
    <p><strong>Secret Key</strong>: {{ $user['secret_key'] }}</p>
    <p><strong>Register Date</strong>: {{ $user['register_at'] }}</p>
    <p>
        <strong>Membership</strong>: {{ strtoupper($user['membership']) }}
        <small>
            @if($user['membership'] === App\Models\User::MEMBERSHIP_PRO)
                <strong>From</strong>: {{ $user['pro_from'] }}
                <strong>To</strong>: {{ $user['pro_to'] }}
            @endif
        </small>
    </p>

    @if($user['membership'] === App\Models\User::MEMBERSHIP_BASIC)
        <hr>
        <h2>Theme</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Version</th>
                    <th>From</th>
                    <th>To</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user->themes as $theme)
                    <tr>
                        <th>{{ $theme['name'] }}</th>
                        <th>{{ $theme->currentVersion['version'] }}</th>
                        <th>{{ $theme->pivot['basic_from'] }}</th>
                        <th>{{ $theme->pivot['basic_to'] }}</th>

                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($user->orders->count() > 0)
        <hr>
        <h2>Order</h2>
        <table>
            <thead>
            <tr>
                <th>Product Name</th>
                <th>Order No.</th>
                <th>Payment Type</th>
                <th>Payment Id</th>
                <th>Price</th>
                <th>Paid Amount</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
            </thead>
            <tbody>
                @foreach($user->orders as $order)
                    <tr>
                        <th>{{ $order->product['name'] }}</th>
                        <th>{{ $order['order_no'] }}</th>
                        <th>{{ $order['payment_type'] }}</th>
                        <th>{{ $order['payment_no'] }}</th>
                        <th>{{ $order['price'] }}</th>
                        <th>{{ $order['paid_amount'] }}</th>
                        <th>{{ $order['status'] }}</th>
                        <th>{{ $order['created_at'] }}</th>
                        <th>{{ $order['updated_at'] }}</th>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>