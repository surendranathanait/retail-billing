<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Denomination;
use App\Models\DenominationTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'customers_count' => Customer::count(),
            'products_count' => Product::count(),
            'invoices_count' => Invoice::count(),
            'invoice_items_count' => InvoiceItem::count(),
            'denominations_count' => Denomination::count(),
            'transactions_count' => DenominationTransaction::count(),
        ];

        $customers = Customer::with('invoices')->get();
        $products = Product::all();
        $invoices = Invoice::with(['customer', 'items'])->get();
        $invoiceItems = InvoiceItem::with(['product', 'invoice'])->get();
        $denominations = Denomination::all();
        $transactions = DenominationTransaction::with('invoice')->get();

        return view('dashboard.index', compact(
            'stats',
            'customers',
            'products',
            'invoices',
            'invoiceItems',
            'denominations',
            'transactions'
        ));
    }

    public function customers()
    {
        $customers = Customer::with('invoices')->paginate(20);
        return view('dashboard.customers', compact('customers'));
    }

    public function products()
    {
        $products = Product::with('invoiceItems')->paginate(20);
        return view('dashboard.products', compact('products'));
    }

    public function invoices()
    {
        $invoices = Invoice::with(['customer', 'items'])->paginate(20);
        return view('dashboard.invoices', compact('invoices'));
    }
}
