<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2e7d32;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .invoice-details {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .total {
            font-size: 1.3em;
            font-weight: bold;
            color: #2e7d32;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.9em;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $organization->name }}</h1>
        <p>Invoice Notification</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $customer->name }},</p>
        
        <p>Thank you for your business! We have generated a new invoice for the agricultural field services provided.</p>
        
        <div class="invoice-details">
            <div class="detail-row">
                <span class="label">Invoice Number:</span>
                <span class="value">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Invoice Date:</span>
                <span class="value">{{ $invoice->issued_at->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Due Date:</span>
                <span class="value">{{ $invoice->due_at->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
        
        <div class="invoice-details">
            <div class="detail-row">
                <span class="label">Subtotal:</span>
                <span class="value">LKR {{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Tax:</span>
                <span class="value">LKR {{ number_format($invoice->tax, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Total Amount:</span>
                <span class="value total">LKR {{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>
        
        @if($invoice->status !== 'paid')
        <div class="alert">
            <strong>Payment Due:</strong> Please arrange payment by {{ $invoice->due_at->format('M d, Y') }} to avoid late fees.
        </div>
        @endif
        
        <p>The detailed invoice is attached to this email as a PDF document. Please review it and contact us if you have any questions.</p>
        
        <p><strong>Payment Information:</strong></p>
        <ul>
            <li>Bank Transfer: {{ $organization->bank_account ?? 'Contact us for details' }}</li>
            <li>Mobile Payment: {{ $organization->mobile_payment ?? 'Contact us for details' }}</li>
            <li>Cash Payment: At our office</li>
        </ul>
        
        <p>If you have already made the payment, please disregard this notice.</p>
        
        <p>Thank you for choosing {{ $organization->name }} for your agricultural service needs!</p>
        
        <p>
            Best regards,<br>
            <strong>{{ $organization->name }}</strong><br>
            @if($organization->phone)Phone: {{ $organization->phone }}<br>@endif
            @if($organization->email)Email: {{ $organization->email }}<br>@endif
            @if($organization->address)Address: {{ $organization->address }}@endif
        </p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ $organization->name }}. All rights reserved.</p>
    </div>
</body>
</html>
