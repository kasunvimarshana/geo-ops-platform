<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-details { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .total { text-align: right; margin-top: 20px; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>{{ $invoice->invoice_number }}</p>
    </div>

    <div class="invoice-details">
        <p><strong>Customer:</strong> {{ $invoice->customer_name }}</p>
        <p><strong>Email:</strong> {{ $invoice->customer_email }}</p>
        <p><strong>Phone:</strong> {{ $invoice->customer_phone }}</p>
        <p><strong>Date:</strong> {{ $invoice->issued_at->format('Y-m-d') }}</p>
        @if($invoice->due_date)
        <p><strong>Due Date:</strong> {{ $invoice->due_date->format('Y-m-d') }}</p>
        @endif
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Service</td>
                <td>{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax_amount > 0)
            <tr>
                <td>Tax</td>
                <td>{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
            @if($invoice->discount_amount > 0)
            <tr>
                <td>Discount</td>
                <td>-{{ $invoice->currency }} {{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="total">
        Total: {{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}
    </div>

    @if($invoice->notes)
    <div style="margin-top: 30px;">
        <p><strong>Notes:</strong></p>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif
</body>
</html>
