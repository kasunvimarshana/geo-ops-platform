<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2e7d32;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .company-info {
            margin-bottom: 30px;
        }
        .company-info h2 {
            color: #2e7d32;
            font-size: 18px;
            margin: 0 0 10px 0;
        }
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-details-left, .invoice-details-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-details h3 {
            color: #2e7d32;
            font-size: 14px;
            margin: 0 0 10px 0;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table thead {
            background-color: #2e7d32;
            color: white;
        }
        .items-table th, .items-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .items-table th {
            font-weight: bold;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 300px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .totals-row.total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #2e7d32;
            border-bottom: 2px solid #2e7d32;
            color: #2e7d32;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-draft { background-color: #f0f0f0; color: #666; }
        .status-sent { background-color: #e3f2fd; color: #1976d2; }
        .status-paid { background-color: #e8f5e9; color: #2e7d32; }
        .status-overdue { background-color: #ffebee; color: #c62828; }
        .status-cancelled { background-color: #fafafa; color: #757575; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <div class="status-badge status-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</div>
    </div>

    <div class="company-info">
        <h2>{{ $organization->name }}</h2>
        <div>{{ $organization->address }}</div>
        @if($organization->phone)
            <div>Phone: {{ $organization->phone }}</div>
        @endif
        @if($organization->email)
            <div>Email: {{ $organization->email }}</div>
        @endif
    </div>

    <div class="invoice-details">
        <div class="invoice-details-left">
            <h3>Bill To:</h3>
            <div><strong>{{ $customer->name }}</strong></div>
            @if($customer->phone)
                <div>Phone: {{ $customer->phone }}</div>
            @endif
            @if($customer->email)
                <div>Email: {{ $customer->email }}</div>
            @endif
            @if($customer->address)
                <div>Address: {{ $customer->address }}</div>
            @endif
        </div>
        <div class="invoice-details-right">
            <div class="info-row">
                <span class="info-label">Invoice No:</span>
                <span>{{ $invoice->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Issue Date:</span>
                <span>{{ $invoice->issued_at->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Due Date:</span>
                <span>{{ $invoice->due_at->format('d M Y') }}</span>
            </div>
            @if($invoice->paid_at)
                <div class="info-row">
                    <span class="info-label">Paid Date:</span>
                    <span>{{ $invoice->paid_at->format('d M Y') }}</span>
                </div>
            @endif
        </div>
    </div>

    @if($job)
        <h3>Job Details</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Area</th>
                    <th>Rate</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $job->service_type }}</strong><br>
                        @if($job->driver)
                            Driver: {{ $job->driver->name }}<br>
                        @endif
                        @if($job->machine)
                            Machine: {{ $job->machine->name }} ({{ $job->machine->type }})<br>
                        @endif
                        @if($job->landMeasurement)
                            Location: {{ $job->landMeasurement->location_name }}
                        @endif
                    </td>
                    <td>
                        @if($job->landMeasurement)
                            {{ number_format($job->landMeasurement->area_hectares, 2) }} hectares<br>
                            ({{ number_format($job->landMeasurement->area_acres, 2) }} acres)
                        @endif
                    </td>
                    <td>
                        @if($job->landMeasurement && $job->landMeasurement->area_hectares > 0)
                            LKR {{ number_format($invoice->subtotal / $job->landMeasurement->area_hectares, 2) }}/ha
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">LKR {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="totals">
        <div class="totals-row">
            <div>Subtotal:</div>
            <div>LKR {{ number_format($invoice->subtotal, 2) }}</div>
        </div>
        @if($invoice->tax > 0)
            <div class="totals-row">
                <div>Tax:</div>
                <div>LKR {{ number_format($invoice->tax, 2) }}</div>
            </div>
        @endif
        <div class="totals-row total">
            <div>Total Amount:</div>
            <div>LKR {{ number_format($invoice->total, 2) }}</div>
        </div>
    </div>

    <div style="margin-top: 40px;">
        <h3>Payment Information</h3>
        <p>Please make payment within {{ $invoice->due_at->diffInDays($invoice->issued_at) }} days of invoice date.</p>
        <p>Thank you for your business!</p>
    </div>

    <div class="footer">
        <p>This is a computer-generated invoice and does not require a signature.</p>
        <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>
</html>
