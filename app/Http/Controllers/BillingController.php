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
        
        // Search in database - fresh query
        $customer = Customer::where('email', $email)->first();

        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone ?? '',
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Customer not found']);
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
            // Get or create customer with fresh data
            $customer = Customer::firstOrCreate(
                ['email' => $validated['email']],
                ['name' => $validated['name']]
            );

            // Always update customer name (in case they provided updated info)
            $customer->update(['name' => $validated['name']]);
            $customer->refresh(); // Refresh to get latest data

            // Generate invoice
            $invoice = $this->billingService->generateInvoice(
                $customer->id,
                $validated['items'],
                (float) $validated['amount_paid']
            );

            return redirect()->route('billing.display', ['invoice' => $invoice->id]);
        } catch (\Exception $e) {
            \Log::error('Bill Generation Error: ' . $e->getMessage());
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
        try {
            $invoice->load(['customer', 'items.product']);
            
            // Generate PDF with proper options
            $pdf = \PDF::loadView('billing.pdf', compact('invoice'))
                ->setPaper('a4')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('enable_remote', true)
                ->setOption('allow_url_fopen', true);
            
            return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF. Please try again.');
        }
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
