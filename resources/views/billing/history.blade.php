<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History</title>
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
            max-width: 1000px;
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
        
        .search-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        
        .search-section label {
            font-weight: bold;
            margin-right: 10px;
        }
        
        .search-section input,
        .search-section button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .search-section button {
            background-color: #0066cc;
            color: white;
            cursor: pointer;
            border: none;
        }
        
        .search-section button:hover {
            background-color: #0052a3;
        }
        
        .customer-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e8f4f8;
            border-left: 4px solid #0066cc;
            border-radius: 4px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        
        .invoice-details {
            display: none;
            background-color: #f0f0f0;
            padding: 15px;
        }
        
        .expand-btn {
            background-color: #0066cc;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .expand-btn:hover {
            background-color: #0052a3;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .items-table th,
        .items-table td {
            padding: 8px;
            border: 1px solid #ccc;
            text-align: left;
            font-size: 13px;
        }
        
        .items-table th {
            background-color: #ddd;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #0066cc;
        }
        
        .pagination .active {
            background-color: #0066cc;
            color: white;
        }
        
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff9933;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .back-btn:hover {
            background-color: #e68a2c;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>🛒 Retail Billing System</h2>
        <div class="navbar-buttons">
            <a href="/" class="nav-btn">📝 New Bill</a>
            <a href="/dashboard" class="nav-btn dashboard">📊 Dashboard</a>
        </div>
    </div>

    <div class="container">
        <h1>Customer Purchase History</h1>
        
        <div class="search-section">
            <label>Search by Email :</label>
            <form method="GET" style="display: flex; gap: 10px;">
                <input type="email" name="email" placeholder="Enter customer email" 
                       value="{{ request('email') }}" required>
                <button type="submit">Search</button>
            </form>
        </div>
        
        @if(isset($customer))
            <div class="customer-info">
                <strong>Customer Name:</strong> {{ $customer->name }}<br>
                <strong>Customer Email:</strong> {{ $customer->email }}
            </div>
        @endif
        
        @if($invoices->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Purchase Date</th>
                        <th>Total Items</th>
                        <th>Total Amount</th>
                        <th>View Details</th>
                        <th>Download PDF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->purchase_date->format('d/m/Y H:i') }}</td>
                            <td>{{ $invoice->items->sum('quantity') }}</td>
                            <td>₹ {{ number_format($invoice->rounded_total, 2) }}</td>
                            <td style="text-align: center;">
                                <button class="expand-btn" onclick="toggleDetails('invoice-{{ $invoice->id }}')">📋 View</button>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('billing.pdf', $invoice->id) }}" class="expand-btn" style="background: #10b981; text-decoration: none; display: inline-block;" target="_blank">📄 Download</a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <div id="invoice-{{ $invoice->id }}" class="invoice-details">
                                    <h4>Items in this invoice:</h4>
                                    <table class="items-table">
                                        <thead>
                                            <tr>
                                                <th>Product Code</th>
                                                <th>Product Name</th>
                                                <th>Unit Price</th>
                                                <th>Quantity</th>
                                                <th>Total with Tax</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->items as $item)
                                                <tr>
                                                    <td>{{ $item->product->product_code }}</td>
                                                    <td>{{ $item->product->name }}</td>
                                                    <td>₹ {{ number_format($item->unit_price, 2) }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>₹ {{ number_format($item->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="pagination">
                {{ $invoices->render() }}
            </div>
        @elseif(request('email'))
            <div class="no-data">
                <p>No purchase history found for this email address.</p>
            </div>
        @else
            <div class="no-data">
                <p>Enter a customer email to view purchase history.</p>
            </div>
        @endif
        
        <a href="/" class="back-btn">Back to Billing</a>
    </div>
    
    <script>
        function toggleDetails(id) {
            const element = document.getElementById(id);
            element.style.display = element.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
