<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Dashboard</title>
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

        .header p {
            color: #666;
            font-size: 14px;
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
            font-size: 14px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 36px;
            color: #667eea;
            font-weight: bold;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .table-title {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 2px solid #667eea;
            font-weight: bold;
            color: #333;
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
            color: #666;
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
        }

        .badge.success {
            background: #10b981;
        }

        .badge.warning {
            background: #f59e0b;
        }

        .badge.danger {
            background: #ef4444;
        }

        .amount {
            color: #10b981;
            font-weight: bold;
        }

        .text-muted {
            color: #999;
            font-size: 13px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            padding: 20px;
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

        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .summary-item {
            background: #f0f7ff;
            padding: 15px;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }

        .summary-item label {
            display: block;
            color: #666;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-item span {
            display: block;
            font-size: 24px;
            color: #667eea;
            font-weight: bold;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .back-btn:hover {
            background: #764ba2;
        }

        .no-data {
            padding: 40px;
            text-align: center;
            color: #999;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 8px;
            }

            .nav-tabs {
                flex-direction: column;
            }

            .nav-tabs a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗄️ Database Dashboard</h1>
            <p>View all database tables and records</p>
            <div class="nav-tabs">
                <a href="/dashboard" class="active">Overview</a>
                <a href="/dashboard/customers">Customers</a>
                <a href="/dashboard/products">Products</a>
                <a href="/dashboard/invoices">Invoices</a>
                <a href="/">← Back to Billing</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Customers</h3>
                <div class="number">{{ $stats['customers_count'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="number">{{ $stats['products_count'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Total Invoices</h3>
                <div class="number">{{ $stats['invoices_count'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Invoice Items</h3>
                <div class="number">{{ $stats['invoice_items_count'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Denominations</h3>
                <div class="number">{{ $stats['denominations_count'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Transactions</h3>
                <div class="number">{{ $stats['transactions_count'] }}</div>
            </div>
        </div>

        <!-- Customers Table -->
        @if($customers->count() > 0)
            <div class="table-container">
                <div class="table-title">👥 Customers (First 10)</div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Total Invoices</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers->take(10) as $customer)
                            <tr>
                                <td><strong>#{{ $customer->id }}</strong></td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td><span class="badge">{{ $customer->invoices->count() }}</span></td>
                                <td class="text-muted">{{ $customer->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Products Table -->
        @if($products->count() > 0)
            <div class="table-container">
                <div class="table-title">📦 Products (First 10)</div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Tax %</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products->take(10) as $product)
                            <tr>
                                <td><strong>#{{ $product->id }}</strong></td>
                                <td><span class="badge success">{{ $product->product_code }}</span></td>
                                <td>{{ $product->name }}</td>
                                <td class="amount">₹{{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->tax_percent }}%</td>
                                <td>
                                    @if($product->stock <= 10)
                                        <span class="badge danger">{{ $product->stock }}</span>
                                    @elseif($product->stock <= 50)
                                        <span class="badge warning">{{ $product->stock }}</span>
                                    @else
                                        <span class="badge success">{{ $product->stock }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Invoices Table -->
        @if($invoices->count() > 0)
            <div class="table-container">
                <div class="table-title">📄 Invoices (First 10)</div>
                <table>
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Subtotal</th>
                            <th>Tax</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices->take(10) as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                <td>{{ $invoice->customer->name }}</td>
                                <td><span class="badge">{{ $invoice->items->count() }}</span></td>
                                <td class="amount">₹{{ number_format($invoice->subtotal, 2) }}</td>
                                <td class="amount">₹{{ number_format($invoice->tax_total, 2) }}</td>
                                <td class="amount"><strong>₹{{ number_format($invoice->rounded_total, 2) }}</strong></td>
                                <td class="text-muted">{{ $invoice->purchase_date->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Denominations Table -->
        @if($denominations->count() > 0)
            <div class="table-container">
                <div class="table-title">💵 Denominations</div>
                <table>
                    <thead>
                        <tr>
                            <th>Denomination</th>
                            <th>Available Count</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($denominations->sortByDesc('value') as $denom)
                            <tr>
                                <td><strong>₹{{ number_format($denom->value, 0) }}</strong></td>
                                <td>{{ $denom->available_count }}</td>
                                <td>
                                    @if($denom->available_count == 0)
                                        <span class="badge danger">Out of Stock</span>
                                    @elseif($denom->available_count < 20)
                                        <span class="badge warning">Low Stock</span>
                                    @else
                                        <span class="badge success">Available</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>
