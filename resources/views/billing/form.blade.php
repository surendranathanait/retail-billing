<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Page</title>
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: inline-block;
            width: 200px;
            font-weight: bold;
            color: #333;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="number"],
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="email"] {
            width: 250px;
        }
        
        select {
            width: 180px;
        }
        
        input[type="number"] {
            width: 150px;
        }
        
        .bill-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }
        
        .bill-items {
            margin: 20px 0;
        }
        
        .item-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            align-items: center;
        }
        
        .item-row input,
        .item-row select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }
        
        .add-btn {
            background-color: #0066cc;
            color: white;
        }
        
        .add-btn:hover {
            background-color: #0052a3;
        }
        
        .remove-btn {
            background-color: #ff9933;
            color: white;
            padding: 8px 15px;
        }
        
        .remove-btn:hover {
            background-color: #e68a2c;
        }
        
        .price-summary {
            background-color: #f0f7ff;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }

        .price-row label {
            width: auto;
            font-weight: bold;
        }

        .price-row .value {
            font-weight: bold;
            color: #667eea;
            font-size: 18px;
        }

        .price-row.total {
            border-top: 2px solid #667eea;
            padding-top: 10px;
            margin-top: 15px;
            font-size: 20px;
        }

        .price-row.total .value {
            color: #10b981;
            font-size: 24px;
        }
        
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }
        
        .cancel-btn {
            background-color: #ff9933;
            color: white;
            padding: 12px 30px;
        }
        
        .cancel-btn:hover {
            background-color: #e68a2c;
        }
        
        .generate-btn {
            background-color: #10b981;
            color: white;
            padding: 12px 30px;
        }
        
        .generate-btn:hover {
            background-color: #059669;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>🛒 Retail Billing System</h2>
        <div class="navbar-buttons">
            <a href="/purchase-history" class="nav-btn">📜 History</a>
            <a href="/dashboard" class="nav-btn dashboard">📊 Dashboard</a>
        </div>
    </div>

    <div class="container">
        <h1>Billing Page</h1>
        
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        
        <form action="{{ route('billing.generate') }}" method="POST" id="billingForm">
            @csrf
            
            <!-- Customer Information -->
            <div class="form-group">
                <label>Customer Email :</label>
                <input type="email" name="email" id="email" placeholder="Email ID" required 
                       value="{{ old('email') }}" onblur="fetchCustomer()">
            </div>
            
            <div class="form-group">
                <label>Customer Name :</label>
                <input type="text" name="name" id="name" placeholder="Name" required 
                       value="{{ old('name') }}">
            </div>
            
            <!-- Bill Section -->
            <div class="bill-section">
                <label style="display: block; font-weight: bold; margin-bottom: 15px;">Bill Section :</label>
                
                <div class="bill-items" id="billItems">
                    <div class="item-row">
                        <select name="items[0][product_id]" class="product-select" onchange="updatePrice()" required>
                            <option value="">Select Product ID</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" 
                                        data-tax="{{ $product->tax_percent }}" data-name="{{ $product->name }}">
                                    {{ $product->product_code }} - {{ $product->name }} (₹{{ number_format($product->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="items[0][quantity]" placeholder="Quantity" min="1" required 
                               onchange="updatePrice()" value="1">
                    </div>
                </div>
                
                <button type="button" class="add-btn" onclick="addItem()">+ Add New Product</button>
            </div>

            <!-- Price Summary -->
            <div class="price-summary">
                <div class="price-row">
                    <label>Subtotal (Before Tax):</label>
                    <span class="value" id="subtotal">₹0.00</span>
                </div>
                <div class="price-row">
                    <label>Total Tax:</label>
                    <span class="value" id="taxTotal">₹0.00</span>
                </div>
                <div class="price-row">
                    <label>Grand Total:</label>
                    <span class="value" id="grandTotal">₹0.00</span>
                </div>
                <div class="price-row">
                    <label>Rounded Down Total:</label>
                    <span class="value" id="roundedTotal">₹0.00</span>
                </div>
                <div class="price-row total">
                    <label>💰 Customer Must Pay:</label>
                    <span class="value" id="customerPays">₹0.00</span>
                </div>
            </div>
            
            <!-- Amount Paid -->
            <div class="form-group bill-section">
                <label>Cash Paid by Customer :</label>
                <input type="number" name="amount_paid" id="amount_paid" placeholder="Amount" step="0.01" min="0" required 
                       value="{{ old('amount_paid') }}" onchange="calculateBalance()">
                <div id="balanceInfo" style="margin-top: 10px; color: #666; font-size: 14px;"></div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <button type="button" class="cancel-btn" onclick="window.location.href='/'">Cancel</button>
                <button type="submit" class="generate-btn">Generate Bill</button>
            </div>
        </form>
    </div>
    
    <script>
        let itemCount = 1;
        const products = @json($products);
        
        function addItem() {
            const billItems = document.getElementById('billItems');
            const newItemHtml = `
                <div class="item-row">
                    <select name="items[${itemCount}][product_id]" class="product-select" onchange="updatePrice()" required>
                        <option value="">Select Product ID</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" 
                                    data-tax="{{ $product->tax_percent }}" data-name="{{ $product->name }}">
                                {{ $product->product_code }} - {{ $product->name }} (₹{{ number_format($product->price, 2) }})
                            </option>
                        @endforeach
                    </select>
                    <input type="number" name="items[${itemCount}][quantity]" placeholder="Quantity" min="1" required 
                           onchange="updatePrice()" value="1">
                    <button type="button" class="remove-btn" onclick="removeItem(this)">Remove</button>
                </div>
            `;
            billItems.insertAdjacentHTML('beforeend', newItemHtml);
            itemCount++;
        }
        
        function removeItem(button) {
            button.parentElement.remove();
            updatePrice();
        }
        
        function fetchCustomer() {
            const email = document.getElementById('email').value;
            if (!email) return;
            
            fetch(`/api/customer-by-email?email=${encodeURIComponent(email)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('name').value = data.customer.name;
                        document.getElementById('name').readOnly = true;
                    } else {
                        document.getElementById('name').readOnly = false;
                        document.getElementById('name').value = '';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function updatePrice() {
            let subtotal = 0;
            let taxTotal = 0;

            const itemRows = document.querySelectorAll('.item-row');
            itemRows.forEach(row => {
                const select = row.querySelector('select');
                const quantityInput = row.querySelector('input[type="number"]');
                
                if (select.value && quantityInput.value) {
                    const option = select.querySelector(`option[value="${select.value}"]`);
                    if (option) {
                        const price = parseFloat(option.dataset.price);
                        const tax = parseFloat(option.dataset.tax);
                        const quantity = parseInt(quantityInput.value);
                        
                        const itemSubtotal = price * quantity;
                        const itemTax = (itemSubtotal * tax) / 100;
                        
                        subtotal += itemSubtotal;
                        taxTotal += itemTax;
                    }
                }
            });

            const grandTotal = subtotal + taxTotal;
            const roundedTotal = Math.floor(grandTotal);

            document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('taxTotal').textContent = '₹' + taxTotal.toFixed(2);
            document.getElementById('grandTotal').textContent = '₹' + grandTotal.toFixed(2);
            document.getElementById('roundedTotal').textContent = '₹' + roundedTotal.toFixed(2);
            document.getElementById('customerPays').textContent = '₹' + roundedTotal.toFixed(2);

            // Update amount paid field to match rounded total
            if (document.getElementById('amount_paid').value === '') {
                document.getElementById('amount_paid').placeholder = `₹${roundedTotal.toFixed(2)} (minimum)`;
            }

            calculateBalance();
        }

        function calculateBalance() {
            const roundedTotal = parseFloat(document.getElementById('roundedTotal').textContent.replace('₹', ''));
            const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
            
            const balanceInfo = document.getElementById('balanceInfo');
            
            if (amountPaid < roundedTotal) {
                balanceInfo.innerHTML = `<span style="color: #ef4444; font-weight: bold;">❌ Insufficient! Need ₹${(roundedTotal - amountPaid).toFixed(2)} more</span>`;
            } else if (amountPaid === roundedTotal) {
                balanceInfo.innerHTML = `<span style="color: #10b981; font-weight: bold;">✅ Exact amount</span>`;
            } else {
                const balance = amountPaid - roundedTotal;
                balanceInfo.innerHTML = `<span style="color: #667eea; font-weight: bold;">💵 Change to return: ₹${balance.toFixed(2)}</span>`;
            }
        }

        // Initialize price on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePrice();
        });
    </script>
</body>
</html>
