<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\BillingService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(private BillingService $billingService) {}

    /**
     * Show billing form
     */
    public function showForm()
    {
        $products = Product::all();
        return view('billing.form', compact('products'));
    }

    /**
     * Get customer details by email (AJAX)
     */
    public function getCustomerByEmail(Request $request)
    {
        $email = $request->query('email');
        $customer = Customer::where('email', $email)->first();

        if ($customer) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * Generate bill
     */
    public function generateBill(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        try {
            // Get or create customer
            $customer = Customer::firstOrCreate(
                ['email' => $validated['email']],
                ['name' => $validated['name']]
            );

            // Generate invoice
            $invoice = $this->billingService->generateInvoice(
                $customer->id,
                $validated['items'],
                (float) $validated['amount_paid']
            );

            return redirect()->route('billing.display', ['invoice' => $invoice->id]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display bill
     */
    public function displayBill(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'denominationTransactions']);
        return view('billing.display', compact('invoice'));
    }

    /**
     * Download invoice PDF
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product']);
        
        $pdf = \PDF::loadView('billing.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Customer purchase history
     */
    public function purchaseHistory(Request $request)
    {
        $email = $request->query('email');
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return view('billing.history', ['invoices' => collect()]);
        }

        $invoices = $customer->invoices()
            ->with('items.product')
            ->orderByDesc('purchase_date')
            ->paginate(10);

        return view('billing.history', compact('invoices', 'customer'));
    }

    /**
     * Get product details by ID (AJAX)
     */
    public function getProduct($id)
    {
        $product = Product::find($id);

        if ($product) {
            return response()->json(['success' => true, 'product' => $product]);
        }

        return response()->json(['success' => false]);
    }
}
