<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices - Database Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .nav-tabs a {
            padding: 10px 20px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .nav-tabs a:hover {
            background: #667eea;
            color: white;
        }

        .nav-tabs a.active {
            background: #667eea;
            color: white;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f0f0f0;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #ddd;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            background: #667eea;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .amount {
            color: #10b981;
            font-weight: bold;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            padding: 20px;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #667eea;
        }

        .pagination .active {
            background: #667eea;
            color: white;
        }

        .text-muted {
            color: #999;
            font-size: 13px;
        }

        .expandable {
            cursor: pointer;
        }

        .items-detail {
            display: none;
            background: #f9f9f9;
            padding: 15px;
        }

        .items-detail.show {
            display: block;
        }

        .items-table {
            width: 100%;
            font-size: 13px;
            border-collapse: collapse;
        }

        .items-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .pdf-download {
            display: inline-block;
            padding: 8px 12px;
            background: #10b981;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }

        .pdf-download:hover {
            background: #059669;
        }

        .action-cell {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📄 All Invoices</h1>
            <div class="nav-tabs">
                <a href="/dashboard">Overview</a>
                <a href="/dashboard/customers">Customers</a>
                <a href="/dashboard/products">Products</a>
                <a href="/dashboard/invoices" class="active">Invoices</a>
                <a href="/">← Back to Billing</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Subtotal</th>
                        <th>Tax</th>
                        <th>Total</th>
                        <th>Balance</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr class="expandable" onclick="toggleDetails({{ $invoice->id }})">
                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td><span class="badge">{{ $invoice->items->count() }}</span></td>
                            <td class="amount">₹{{ number_format($invoice->subtotal, 2) }}</td>
                            <td class="amount">₹{{ number_format($invoice->tax_total, 2) }}</td>
                            <td class="amount"><strong>₹{{ number_format($invoice->rounded_total, 2) }}</strong></td>
                            <td class="amount">₹{{ number_format($invoice->balance_returned, 2) }}</td>
                            <td class="text-muted">{{ $invoice->purchase_date->format('Y-m-d H:i') }}</td>
                            <td class="action-cell">
                                <a href="{{ route('billing.pdf', $invoice->id) }}" class="pdf-download" target="_blank">📄 PDF</a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                <div id="detail-{{ $invoice->id }}" class="items-detail">
                                    <h4 style="margin-bottom: 10px;">Invoice Items:</h4>
                                    <table class="items-table">
                                        <tr style="background: #f0f0f0;">
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Price</th>
                                            <th>Tax %</th>
                                            <th>Tax</th>
                                            <th>Total</th>
                                        </tr>
                                        @foreach($invoice->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                                <td>₹{{ number_format($item->purchase_price, 2) }}</td>
                                                <td>{{ $item->tax_percent }}%</td>
                                                <td>₹{{ number_format($item->tax_amount, 2) }}</td>
                                                <td><strong>₹{{ number_format($item->total, 2) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #999;">No invoices found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($invoices->hasPages())
                <div class="pagination">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleDetails(id) {
            const detail = document.getElementById('detail-' + id);
            if (detail) {
                detail.classList.toggle('show');
            }
        }
    </script>
</body>
</html>
