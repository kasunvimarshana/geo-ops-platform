<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .invoice {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .details {
            margin-bottom: 20px;
        }
        .details h2 {
            margin: 0 0 10px;
        }
        .details p {
            margin: 5px 0;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items th, .items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="header">
            <h1>Invoice</h1>
            <p>Invoice Number: {{ $invoice->id }}</p>
            <p>Date: {{ $invoice->created_at->format('d M, Y') }}</p>
        </div>
        <div class="details">
            <h2>Billing Details</h2>
            <p>Name: {{ $invoice->customer->name }}</p>
            <p>Email: {{ $invoice->customer->email }}</p>
            <p>Address: {{ $invoice->customer->address }}</p>
        </div>
        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total">
            Total Amount: {{ number_format($invoice->total_amount, 2) }}
        </div>
    </div>
</body>
</html>