<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InsightsController extends Controller
{
    /**
     * Case 1: High-Variety Customer - customers who purchased 5+ distinct products in a single day
     */
    public function highVarietyCustomers()
    {
        $customers = Invoice::select(
            'customer_id',
            DB::raw('DATE(purchase_date) as purchase_date'),
            DB::raw('COUNT(DISTINCT invoice_items.product_id) as distinct_products'),
            DB::raw('SUM(invoices.rounded_total) as total_amount'),
            DB::raw('SUM(invoices.tax_total) as total_tax'),
            DB::raw('SUM(invoice_items.quantity) as total_items')
        )
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->groupBy('customer_id', DB::raw('DATE(purchase_date)'))
            ->havingRaw('COUNT(DISTINCT invoice_items.product_id) >= 5')
            ->with('customer')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->map(function ($record) {
                return [
                    'customer_id' => $record->customer_id,
                    'customer_name' => $record->customer?->name,
                    'customer_email' => $record->customer?->email,
                    'purchase_date' => $record->purchase_date,
                    'distinct_products' => $record->distinct_products,
                    'total_amount' => (float) $record->total_amount,
                    'total_tax_paid' => (float) $record->total_tax,
                    'total_items_purchased' => (int) $record->total_items,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $customers,
            'message' => 'High-variety customers (5+ distinct products in single day)',
        ]);
    }

    /**
     * Case 2: Stock Forecast - average daily sales for last 7 days and estimated days until stock runs out
     */
    public function stockForecast()
    {
        $last7Days = now()->subDays(7);

        $productStats = InvoiceItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_sold'),
            DB::raw('COUNT(DISTINCT DATE(invoice_items.created_at)) as days_sold')
        )
            ->whereHas('invoice', function ($query) use ($last7Days) {
                $query->where('purchase_date', '>=', $last7Days);
            })
            ->groupBy('product_id')
            ->with('product')
            ->get()
            ->map(function ($stat) {
                $avgDailySales = $stat->days_sold > 0 ? $stat->total_sold / $stat->days_sold : 0;
                $currentStock = $stat->product->stock;
                $daysUntilStockout = $avgDailySales > 0 ? round($currentStock / $avgDailySales, 2) : 999;

                return [
                    'product_id' => $stat->product_id,
                    'product_name' => $stat->product->name,
                    'current_stock' => $currentStock,
                    'avg_daily_sales_last_7_days' => round($avgDailySales, 2),
                    'estimated_days_until_stockout' => $daysUntilStockout,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $productStats,
            'message' => 'Stock forecast based on 7-day average sales',
        ]);
    }

    /**
     * Case 3: Repeat Customer Insights - customers with 2nd purchase within 7 days of first
     */
    public function repeatCustomerInsights()
    {
        // Get customers with multiple purchases
        $customerInvoices = Invoice::select('customer_id')
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) >= 2')
            ->pluck('customer_id');

        $repeatCustomers = collect();

        foreach ($customerInvoices as $customerId) {
            $invoices = Invoice::where('customer_id', $customerId)
                ->orderBy('purchase_date')
                ->get();

            // Find pairs within 7 days
            for ($i = 0; $i < count($invoices) - 1; $i++) {
                $firstPurchase = $invoices[$i];
                $secondPurchase = $invoices[$i + 1];
                $daysBetween = $firstPurchase->purchase_date->diffInDays($secondPurchase->purchase_date);

                if ($daysBetween <= 7 && $daysBetween > 0) {
                    $totalSpent = collect([$firstPurchase, $secondPurchase])
                        ->sum('rounded_total');

                    $repeatCustomers->push([
                        'customer_id' => $customerId,
                        'customer_name' => $firstPurchase->customer->name,
                        'customer_email' => $firstPurchase->customer->email,
                        'first_purchase_date' => $firstPurchase->purchase_date->format('Y-m-d H:i:s'),
                        'second_purchase_date' => $secondPurchase->purchase_date->format('Y-m-d H:i:s'),
                        'days_between_purchases' => $daysBetween,
                        'total_spending_in_window' => (float) $totalSpent,
                    ]);
                }
            }
        }

        $repeatCustomers = $repeatCustomers->sortByDesc('total_spending_in_window')->take(5);

        return response()->json([
            'success' => true,
            'data' => $repeatCustomers->values(),
            'message' => 'Customers with repeat purchases within 7 days (Last 5)',
        ]);
    }

    /**
     * Case 4: High-Demand Orders - invoices containing top 5 most sold products in last 30 days
     */
    public function highDemandOrders()
    {
        $last30Days = now()->subDays(30);

        // Get top 5 most sold products in last 30 days
        $topProducts = InvoiceItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('invoice', function ($query) use ($last30Days) {
                $query->where('purchase_date', '>=', $last30Days);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->pluck('product_id');

        // Get invoices containing these products
        $highDemandInvoices = Invoice::whereHas('items', function ($query) use ($topProducts) {
            $query->whereIn('product_id', $topProducts);
        })
            ->with(['customer', 'items.product'])
            ->where('purchase_date', '>=', $last30Days)
            ->orderByDesc('rounded_total')
            ->get()
            ->map(function ($invoice) use ($topProducts) {
                // Filter items to show only high-demand products
                $highDemandItems = $invoice->items
                    ->filter(fn($item) => $topProducts->contains($item->product_id))
                    ->map(fn($item) => [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'total_with_tax' => (float) $item->total,
                    ]);

                return [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_name' => $invoice->customer->name,
                    'customer_email' => $invoice->customer->email,
                    'purchase_date' => $invoice->purchase_date->format('Y-m-d H:i:s'),
                    'high_demand_items' => $highDemandItems,
                    'invoice_total' => (float) $invoice->rounded_total,
                    'tax_paid' => (float) $invoice->tax_total,
                ];
            });

        return response()->json([
            'success' => true,
            'top_5_products' => $topProducts,
            'data' => $highDemandInvoices,
            'message' => 'Invoices with high-demand products (top 5 sold in last 30 days)',
        ]);
    }
}
