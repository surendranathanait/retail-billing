<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            background: white;
        }

        .container {
            max-width: 850px;
            margin: 0 auto;
            padding: 30px 20px;
            background: white;
        }

        /* Header Section */
        .header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .company-info h1 {
            color: #667eea;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-info p {
            color: #999;
            font-size: 11px;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            color: #667eea;
            font-size: 22px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .invoice-details p {
            margin: 3px 0;
            font-size: 12px;
            color: #555;
        }

        .invoice-details strong {
            color: #333;
        }

        /* Customer Info Section */
        .info-section {
            display: flex;
            gap: 30px;
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .info-block {
            flex: 1;
        }

        .info-block h3 {
            color: #667eea;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-block p {
            font-size: 12px;
            color: #555;
            margin: 3px 0;
            line-height: 1.6;
        }

        /* Items Table */
        .items-section {
            margin-bottom: 25px;
        }

        .items-section h3 {
            color: #667eea;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 11px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: none;
        }

        th.amount {
            text-align: right;
        }

        tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        td {
            padding: 10px 8px;
            font-size: 12px;
            color: #555;
        }

        td.amount {
            text-align: right;
            font-weight: 500;
        }

        .code {
            color: #667eea;
            font-weight: 600;
            font-size: 11px;
        }

        .total-amount {
            font-weight: bold;
            color: #667eea;
        }

        /* Summary Section */
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }

        .summary {
            width: 350px;
        }

        .summary-box {
            background: #f8f9fa;
            border: 2px solid #667eea;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 12px;
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            color: #666;
            font-weight: 500;
        }

        .summary-value {
            color: #333;
            font-weight: 600;
        }

        .total-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 15px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .total-box .summary-label,
        .total-box .summary-value {
            color: white;
            font-size: 13px;
            font-weight: bold;
        }

        .payment-box {
            background: #d4edda;
            border: 2px solid #28a745;
            border-radius: 5px;
            padding: 12px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .payment-row:last-child {
            margin-bottom: 0;
        }

        .payment-row .label {
            color: #155724;
            font-weight: 600;
        }

        .payment-row .amount {
            color: #155724;
            font-weight: bold;
            font-size: 13px;
        }

        /* Footer Section */
        .footer {
            border-top: 2px solid #667eea;
            padding-top: 15px;
            text-align: center;
            margin-top: 30px;
        }

        .footer p {
            color: #999;
            font-size: 11px;
            margin: 4px 0;
        }

        .footer .thank-you {
            color: #667eea;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .footer .disclaimer {
            font-size: 10px;
            color: #bbb;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="company-info">
                    <h1>RETAIL STORE</h1>
                    <p>Professional Billing Solution</p>
                </div>
                <div class="invoice-details">
                    <h2>INVOICE</h2>
                    <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                    <p><strong>Date:</strong> {{ $invoice->purchase_date->format('d/m/Y') }}</p>
                    <p><strong>Time:</strong> {{ $invoice->purchase_date->format('H:i:s') }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="info-section">
            <div class="info-block">
                <h3>Bill To</h3>
                <p><strong>{{ $invoice->customer->name }}</strong></p>
                <p>{{ $invoice->customer->email }}</p>
            </div>
            <div class="info-block">
                <h3>Invoice Info</h3>
                <p><strong>Date:</strong> {{ $invoice->purchase_date->format('d M, Y') }}</p>
                <p><strong>Status:</strong> <span style="color: #10b981; font-weight: bold;">Completed</span></p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="items-section">
            <h3>Invoice Items</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">Code</th>
                        <th style="width: 32%;">Product Name</th>
                        <th class="amount" style="width: 11%;">Unit Price</th>
                        <th class="amount" style="width: 8%;">Qty</th>
                        <th class="amount" style="width: 11%;">Subtotal</th>
                        <th class="amount" style="width: 8%;">Tax %</th>
                        <th class="amount" style="width: 14%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td><span class="code">{{ $item->product->product_code }}</span></td>
                            <td>{{ $item->product->name }}</td>
                            <td class="amount">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="amount">{{ $item->quantity }}</td>
                            <td class="amount">₹{{ number_format($item->purchase_price, 2) }}</td>
                            <td class="amount">{{ number_format($item->tax_percent, 2) }}%</td>
                            <td class="amount total-amount">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary">
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="summary-label">Subtotal:</span>
                        <span class="summary-value">₹{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total Tax:</span>
                        <span class="summary-value">₹{{ number_format($invoice->tax_total, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Grand Total:</span>
                        <span class="summary-value">₹{{ number_format($invoice->grand_total, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Rounding:</span>
                        <span class="summary-value">₹{{ number_format($invoice->rounded_total - $invoice->grand_total, 2) }}</span>
                    </div>
                </div>

                <div class="total-box">
                    <span class="summary-label">Amount to Pay:</span>
                    <span class="summary-value">₹{{ number_format($invoice->rounded_total, 2) }}</span>
                </div>

                <div class="payment-box">
                    <div class="payment-row">
                        <span class="label">Amount Paid:</span>
                        <span class="amount">₹{{ number_format($invoice->amount_paid, 2) }}</span>
                    </div>
                    @if($invoice->balance_returned > 0)
                        <div class="payment-row" style="border-top: 1px solid #28a745; padding-top: 8px; margin-top: 8px;">
                            <span class="label">Change/Balance:</span>
                            <span class="amount">₹{{ number_format($invoice->balance_returned, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="thank-you">Thank you for your purchase!</p>
            <p>We appreciate your business and look forward to serving you again.</p>
            <p class="disclaimer">This is a computer-generated invoice. No signature is required.</p>
        </div>
    </div>
</body>
</html>
