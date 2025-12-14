<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Page - Invoice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .navbar {
            background-color: #333;
            padding: 0 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            color: white;
            margin: 0;
        }

        .navbar-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            padding: 10px 15px;
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .nav-btn:hover {
            background-color: #5568d3;
        }

        .nav-btn.dashboard {
            background-color: #10b981;
        }

        .nav-btn.dashboard:hover {
            background-color: #059669;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        
        .header-info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            padding-bottom: 15px;
            border-bottom: 2px solid #ddd;
        }
        
        .header-info label {
            font-weight: bold;
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th {
            background-color: #f0f0f0;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        
        td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f0f7ff;
            border: 2px solid #667eea;
            border-radius: 8px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-row.total {
            border-top: 2px solid #667eea;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .summary-row.total .summary-value {
            color: #10b981;
            font-size: 20px;
        }
        
        .summary-label {
            font-weight: bold;
            color: #333;
        }
        
        .summary-value {
            color: #667eea;
            font-weight: bold;
        }
        
        .denomination-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 4px;
        }
        
        .denomination-section h3 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .denomination-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 8px 0;
        }
        
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        button, a.btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        
        .back-btn {
            background-color: #ff9933;
            color: white;
        }
        
        .back-btn:hover {
            background-color: #e68a2c;
        }
        
        .pdf-btn {
            background-color: #10b981;
            color: white;
        }
        
        .pdf-btn:hover {
            background-color: #059669;
        }

        .new-bill-btn {
            background-color: #667eea;
            color: white;
        }

        .new-bill-btn:hover {
            background-color: #5568d3;
        }

        @media print {
            .navbar, .action-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>🛒 Retail Billing System</h2>
        <div class="navbar-buttons">
            <a href="/" class="nav-btn">📝 New Bill</a>
            <a href="/purchase-history" class="nav-btn">📜 History</a>
            <a href="/dashboard" class="nav-btn dashboard">📊 Dashboard</a>
        </div>
    </div>

    <div class="container">
        <h1>Invoice</h1>
        
        <!-- Header Information -->
        <div class="header-info">
            <div>
                <label>Customer Email :</label> {{ $invoice->customer->email }}
            </div>
            <div>
                <label>Invoice Number :</label> {{ $invoice->invoice_number }}
            </div>
        </div>
        
        <!-- Bill Section -->
        <h2>Bill Section :</h2>
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Purchase Price</th>
                    <th>Tax % for Item</th>
                    <th>Tax payable for Item</th>
                    <th>Total price of the Item</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product->product_code }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->purchase_price, 2) }}</td>
                        <td>{{ number_format($item->tax_percent, 2) }}%</td>
                        <td>{{ number_format($item->tax_amount, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-row">
                <span class="summary-label">Total Price without Tax :</span>
                <span class="summary-value">₹{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Tax Payable :</span>
                <span class="summary-value">₹{{ number_format($invoice->tax_total, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Net Price of Purchased Items :</span>
                <span class="summary-value">₹{{ number_format($invoice->grand_total, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Rounded Down Value :</span>
                <span class="summary-value">₹{{ number_format($invoice->rounded_total, 2) }}</span>
            </div>
            <div class="summary-row total">
                <span class="summary-label">💰 Customer Must Pay :</span>
                <span class="summary-value">₹{{ number_format($invoice->rounded_total, 2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Balance Returned :</span>
                <span class="summary-value">₹{{ number_format($invoice->balance_returned, 2) }}</span>
            </div>
        </div>
        
        <!-- Denomination Breakdown -->
        @if($invoice->denominationTransactions->count() > 0)
            <div class="denomination-section">
                <h3>Balance Denomination :</h3>
                <div class="denomination-row">
                    @foreach($invoice->denominationTransactions as $transaction)
                        <div>
                            <strong>₹ {{ number_format($transaction->denomination, 0) }} :</strong> {{ $transaction->count_used }}
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="denomination-section">
                <h3>Balance Denomination :</h3>
                <p>No balance returned.</p>
            </div>
        @endif
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="back-btn" onclick="window.print()">🖨️ Print</button>
            <a href="{{ route('billing.pdf', $invoice->id) }}" class="btn pdf-btn">📄 Download PDF</a>
            <a href="/purchase-history" class="btn back-btn">📜 View History</a>
            <a href="/" class="btn new-bill-btn">➕ Create New Bill</a>
        </div>
    </div>
</body>
</html>
