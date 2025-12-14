<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Database Dashboard</title>
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
            max-width: 1200px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 All Products</h1>
            <div class="nav-tabs">
                <a href="/dashboard">Overview</a>
                <a href="/dashboard/customers">Customers</a>
                <a href="/dashboard/products" class="active">Products</a>
                <a href="/dashboard/invoices">Invoices</a>
                <a href="/">← Back to Billing</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Tax %</th>
                        <th>Stock</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
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
                            <td class="text-muted">{{ $product->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #999;">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($products->hasPages())
                <div class="pagination">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
