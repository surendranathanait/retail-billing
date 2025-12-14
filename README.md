# Retail Billing & Customer Purchase Tracking System

A Laravel-based billing and customer purchase tracking system designed for retail operations with support for multi-product invoicing, tax calculations, denomination-based change management, and business analytics.

## Overview

This system streamlines billing operations, inventory tracking, and customer management for retail stores. It handles complex billing scenarios with tax calculations, automated change denomination breakdown, and provides business insights through dedicated APIs.

## Features Implemented

### 1. Billing & Invoice Management
- **Customer Management**: Customers are automatically created or retrieved based on email
- **Multi-Product Invoicing**: Support for adding multiple products with quantities in a single invoice
- **Tax Calculation**: Individual product taxes are calculated and summed for the invoice
- **Rounding Logic**: Invoice totals are rounded down to the nearest whole number
- **Balance Calculation**: Automatic calculation of balance to be returned to customer

### 2. Denomination-Based Change Management
- **Automated Breakdown**: Balance is automatically broken down into available denominations (₹2000, ₹500, ₹200, ₹100, ₹50, ₹20, ₹10, ₹5, ₹2, ₹1)
- **Denomination Tracking**: Used denominations are recorded for auditing purposes
- **Stock Management**: Denomination availability is tracked and updated with each transaction
- **Greedy Algorithm**: Uses denomination values from largest to smallest to minimize number of notes/coins

### 3. Invoice & Bill Display
- **Two-Page Format**: 
  - **Page 1**: Billing form for customer and product entry
  - **Page 2**: Invoice summary with itemized breakdown, tax calculations, and denomination details
- **PDF Generation**: Invoices can be downloaded as PDFs for record keeping
- **Email Integration**: PDFs can be sent to customer emails (requires email configuration)

### 4. Purchase History
- **Customer Lookup**: Search customers by email
- **Order History**: View all previous purchases with date and amount
- **Detailed Items**: Expand orders to view individual items purchased

### 5. Business Insights APIs

#### Case 1: High-Variety Customers
- **Endpoint**: `GET /api/insights/high-variety-customers`
- **Returns**: Top 5 customers who purchased 5+ distinct products in a single day
- **Data**: Customer name, email, total amount spent, total tax paid, total items purchased
- **Optimization**: Uses GROUP BY with HAVING clause for efficient grouping

#### Case 2: Stock Forecast
- **Endpoint**: `GET /api/insights/stock-forecast`
- **Returns**: Average daily sales for last 7 days and estimated days until stockout
- **Formula**: `Days until stockout = Current Stock / Average Daily Sales`
- **Optimization**: Filters last 7 days and groups by product for efficient calculation

#### Case 3: Repeat Customer Insights
- **Endpoint**: `GET /api/insights/repeat-customers`
- **Returns**: Last 5 customers with 2nd purchase within 7 days of first purchase
- **Data**: First purchase date, second purchase date, total spending in window
- **Logic**: Iterates through customer invoices to find repeat purchases within 7 days

#### Case 4: High-Demand Orders
- **Endpoint**: `GET /api/insights/high-demand-orders`
- **Returns**: Invoices containing top 5 most-sold products in last 30 days
- **Scope**: Only shows items from high-demand products in each invoice
- **Optimization**: Uses subquery for top products and filters invoices efficiently

## Database Design

### Tables Created

#### `customers`
- `id`: Primary key
- `name`: Customer name
- `email`: Unique email address (indexed for fast lookups)
- `timestamps`: Created/updated timestamps

#### `products`
- `id`: Primary key
- `name`: Product name
- `product_code`: Unique product identifier (indexed)
- `stock`: Available quantity
- `price`: Price per unit (2 decimals)
- `tax_percent`: Tax percentage applicable
- `timestamps`: Created/updated timestamps

#### `invoices`
- `id`: Primary key
- `invoice_number`: Unique invoice identifier
- `customer_id`: Foreign key to customers table
- `subtotal`: Total before tax (2 decimals)
- `tax_total`: Sum of all taxes (2 decimals)
- `grand_total`: Subtotal + tax (2 decimals)
- `rounded_total`: Grand total rounded down (2 decimals)
- `amount_paid`: Cash paid by customer (2 decimals)
- `balance_returned`: Change amount (2 decimals)
- `payment_mode`: Type of payment (enum: cash)
- `purchase_date`: Transaction timestamp (indexed for date-based queries)
- `timestamps`: Created/updated timestamps

#### `invoice_items`
- `id`: Primary key
- `invoice_id`: Foreign key to invoices (cascade delete)
- `product_id`: Foreign key to products
- `quantity`: Number of units purchased
- `unit_price`: Price per unit at time of purchase
- `purchase_price`: Subtotal for this item (unit_price × quantity)
- `tax_percent`: Tax rate applied
- `tax_amount`: Calculated tax for this item
- `total`: Purchase price + tax amount
- `timestamps`: Created/updated timestamps

#### `denominations`
- `id`: Primary key
- `value`: Currency denomination value (unique, indexed)
- `available_count`: Number of notes/coins available
- `timestamps`: Created/updated timestamps

#### `denomination_transactions`
- `id`: Primary key
- `invoice_id`: Foreign key to invoices (cascade delete)
- `denomination`: Denomination value used
- `count_used`: Number of notes/coins used for this denomination
- `timestamps`: Created/updated timestamps

## Key Design Decisions

### 1. Rounding Strategy
- **Decision**: Round invoice totals DOWN to nearest whole number
- **Justification**: Aligns with retail billing practices; benefits customer
- **Implementation**: Used PHP's `floor()` function

### 2. Denomination Breakdown
- **Decision**: Greedy algorithm (largest denomination first)
- **Justification**: Minimizes number of notes/coins to handle
- **Fallback**: Smaller denominations used if exact change not possible with larger ones
- **Tracking**: Each denomination transaction recorded for auditing

### 3. Stock Management
- **Decision**: Decrement stock immediately upon invoice creation
- **Justification**: Prevents overselling; real-time inventory accuracy
- **Transaction**: Wrapped in database transaction to ensure consistency

### 4. Customer Auto-Fill
- **Decision**: If email exists, auto-populate name; otherwise allow manual entry
- **Implementation**: AJAX call to check email existence
- **Benefit**: Faster checkout for repeat customers, flexibility for new ones

### 5. Query Optimization
- **Indexes**: Added indexes on frequently queried columns (email, purchase_date, product_code)
- **Relationships**: Used eager loading (with()) to prevent N+1 queries
- **Grouping**: Used raw SQL for complex aggregations with GROUP BY and HAVING

### 6. Invoice Numbering
- **Format**: `INV-{6-digit-sequence}-{timestamp}`
- **Example**: `INV-000001-20251214150830`
- **Benefit**: Unique, sortable, human-readable

## Performance Considerations

### For Large Datasets (1 Crore Invoices, 10+ Lakh Customers, 1+ Lakh Products)

1. **Indexing Strategy**:
   - Composite indexes on frequently filtered columns
   - Indexes on foreign keys for JOIN operations
   - Index on purchase_date for range queries

2. **Query Optimization**:
   - Pagination for list endpoints (10 records per page)
   - Eager loading to prevent N+1 queries
   - Raw SQL for complex aggregations instead of Eloquent collections

3. **Partitioning Considerations** (Future):
   - Can partition invoices table by date ranges
   - Can partition invoice_items by invoice_id ranges

4. **Caching** (Recommended):
   - Cache top products for last 30 days (refreshed daily)
   - Cache stock forecast data (refreshed hourly)

## API Response Format

All API endpoints return JSON in the following format:

```json
{
  "success": true,
  "data": [...],
  "message": "Description of the data"
}
```

## Installation & Setup

### Prerequisites
- PHP 8.2+
- Laravel 12
- MySQL/MariaDB
- Composer
- npm (for frontend assets)

### Installation Steps

```bash
# Clone repository
cd retail-billing

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

The application will be available at `http://localhost:8000`

## Usage

### Billing (Web)
1. Navigate to http://localhost:8000
2. Enter customer email - name auto-fills if customer exists
3. Add products with quantities
4. Enter amount paid by customer
5. Click "Generate Bill" to see invoice and denomination breakdown
6. Download PDF for record keeping

### Purchase History
1. Navigate to http://localhost:8000/purchase-history
2. Enter customer email
3. View all previous purchases
4. Click "View Details" to see items in each invoice

### API Endpoints

```
GET /api/insights/high-variety-customers     - Top customers with 5+ distinct products
GET /api/insights/stock-forecast              - Stock levels and days until stockout
GET /api/insights/repeat-customers            - Customers with repeat purchases in 7 days
GET /api/insights/high-demand-orders          - Invoices with top 5 products
```

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── BillingController.php         - Main billing operations
│   │   └── Api/
│   │       └── InsightsController.php    - Business insights APIs
│   └── Middleware/
├── Models/
│   ├── Customer.php
│   ├── Product.php
│   ├── Invoice.php
│   ├── InvoiceItem.php
│   ├── Denomination.php
│   └── DenominationTransaction.php
├── Services/
│   └── BillingService.php                - Core billing logic
database/
├── migrations/                            - Database schema
└── seeders/                               - Sample data
resources/
└── views/
    └── billing/
        ├── form.blade.php                - Billing form (Page 1)
        ├── display.blade.php             - Invoice display (Page 2)
        ├── pdf.blade.php                 - PDF template
        └── history.blade.php             - Purchase history
routes/
├── web.php                               - Web routes
└── api.php                               - API routes
```

## Assumptions Made

1. **Currency**: All amounts in Indian Rupees (₹)
2. **Rounding**: Invoice totals rounded DOWN, not to nearest
3. **Payment Mode**: Only cash payment supported (enum: 'cash')
4. **Tax Calculation**: Tax calculated per item, then summed
5. **Denomination Coverage**: Assumes available denominations can cover most balances
6. **Stock Validation**: Stock checked before invoice creation
7. **Email Requirement**: Customer email is required for all transactions
8. **No Returns/Refunds**: System doesn't handle order cancellations
9. **Single Currency**: System handles only Indian Rupee denominations
10. **Transaction Integrity**: All operations use database transactions

## Known Limitations

1. **Email Sending**: Requires SMTP configuration for actual email delivery
2. **Large PDF Generation**: May be slow for invoices with 100+ items
3. **Denomination Breakdowns**: Assumes remaining balance < 1 after greedy algorithm
4. **No Batch Operations**: Each invoice created individually
5. **API Pagination**: Fixed page sizes (no user-configurable pagination)

## Future Enhancements

1. Bulk invoice import/export
2. Customer loyalty programs
3. Inventory alerts and auto-reordering
4. Advanced payment methods (card, UPI)
5. Multi-store support
6. Invoice modifications/amendments
7. GST compliance reporting
8. Customer analytics dashboard
9. Automatic email receipts with retry logic
10. API rate limiting and authentication

## Security Notes

1. All monetary values use 2 decimal precision
2. Stock decrements are atomic (transactional)
3. Foreign key constraints enforce referential integrity
4. Email validation prevents invalid customer records
5. Unique product codes prevent duplicates

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test tests/Feature/BillingTest.php
```

## Support & Debugging

For debugging, check:
- `storage/logs/laravel.log` - Application logs
- `storage/framework/cache/` - Cache files
- Database for transaction records

## License

MIT License - See LICENSE.md

## Author

Retail Billing System v1.0

---

**Last Updated**: December 14, 2025

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
