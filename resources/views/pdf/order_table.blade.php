<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zamówienie #{{ $order->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .producer {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Zamówienie #{{ $order->id }}</h1>
    <p>Producent: {{ $producer_name }}</p>
    <p>Status: {{ ucfirst($order->status) }}</p>
    <p>Wartość Całkowita: {{ number_format($order->total_value, 2) }} PLN</p>
    <p>Utworzono: {{ $order->created_at->format('Y-m-d H:i') }}</p>
</div>

<div class="producer">
    <h2>Pozycje Zamówienia</h2>
</div>

<table>
    <thead>
    <tr>
        <th>Produkt</th>
        <th>Ilość</th>
        <th>Cena za sztukę (PLN)</th>
        <th>Cena całkowita (PLN)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($productsForOrder as $item)
        <tr>
            <td>{{ $item['name'] }}</td>
{{--            <td>{{ $item['quantity'] }}</td>--}}
{{--            <td>{{ number_format($item['unit_price'], 2) }}</td>--}}
{{--            <td>{{ number_format($item['quantity'] * $item['unit_price'], 2) }}</td>--}}
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
