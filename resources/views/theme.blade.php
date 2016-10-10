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
<h2>Theme</h2>
@if($products->count() > 0)
    <table>
        <thead>
        <tr>
            <th>Theme Name</th>
            <th>Version</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <th><a href="{{ url('/plan/basic') . '?theme=' . $product['name'] }}">{{ $product['name'] }}</a></th>
                <th>{{ $product->theme->currentVersion['version'] }}</th>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>