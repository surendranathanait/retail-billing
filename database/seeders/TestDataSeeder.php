<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Seed test data for demonstration
     */
    public function run(): void
    {
        // Get existing data
        $customers = Customer::all();
        $products = Product::all();

        // Create sample invoices for testing
        foreach ($customers->take(3) as $customer) {
            // Create invoice 1 for this customer (today)
            $invoice1 = Invoice::create([
                'invoice_number' => 'INV-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT) . '-' . now()->format('YmdHis'),
                'customer_id' => $customer->id,
                'subtotal' => 0,
                'tax_total' => 0,
                'grand_total' => 0,
                'rounded_total' => 0,
                'amount_paid' => 0,
                'balance_returned' => 0,
                'payment_mode' => 'cash',
                'purchase_date' => now(),
            ]);

            // Add 6 items to reach high-variety requirement
            $itemProducts = $products->random(6);
            $subtotal = 0;
            $taxTotal = 0;

            foreach ($itemProducts as $product) {
                $qty = rand(1, 3);
                $purchasePrice = $product->price * $qty;
                $taxAmount = ($purchasePrice * $product->tax_percent) / 100;
                $total = $purchasePrice + $taxAmount;

                InvoiceItem::create([
                    'invoice_id' => $invoice1->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'purchase_price' => $purchasePrice,
                    'tax_percent' => $product->tax_percent,
                    'tax_amount' => $taxAmount,
                    'total' => $total,
                ]);

                $subtotal += $purchasePrice;
                $taxTotal += $taxAmount;
            }

            $grandTotal = $subtotal + $taxTotal;
            $roundedTotal = floor($grandTotal);
            $amountPaid = $roundedTotal + 500;
            $balance = $amountPaid - $roundedTotal;

            $invoice1->update([
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'rounded_total' => $roundedTotal,
                'amount_paid' => $amountPaid,
                'balance_returned' => $balance,
            ]);

            // Create invoice 2 within 7 days (for repeat customer test)
            $invoice2 = Invoice::create([
                'invoice_number' => 'INV-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT) . '-' . now()->addDays(3)->format('YmdHis'),
                'customer_id' => $customer->id,
                'subtotal' => 0,
                'tax_total' => 0,
                'grand_total' => 0,
                'rounded_total' => 0,
                'amount_paid' => 0,
                'balance_returned' => 0,
                'payment_mode' => 'cash',
                'purchase_date' => now()->addDays(3),
            ]);

            // Add 3 items to invoice 2
            $itemProducts2 = $products->random(3);
            $subtotal2 = 0;
            $taxTotal2 = 0;

            foreach ($itemProducts2 as $product) {
                $qty = rand(1, 2);
                $purchasePrice = $product->price * $qty;
                $taxAmount = ($purchasePrice * $product->tax_percent) / 100;
                $total = $purchasePrice + $taxAmount;

                InvoiceItem::create([
                    'invoice_id' => $invoice2->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'purchase_price' => $purchasePrice,
                    'tax_percent' => $product->tax_percent,
                    'tax_amount' => $taxAmount,
                    'total' => $total,
                ]);

                $subtotal2 += $purchasePrice;
                $taxTotal2 += $taxAmount;
            }

            $grandTotal2 = $subtotal2 + $taxTotal2;
            $roundedTotal2 = floor($grandTotal2);
            $amountPaid2 = $roundedTotal2 + 200;
            $balance2 = $amountPaid2 - $roundedTotal2;

            $invoice2->update([
                'subtotal' => $subtotal2,
                'tax_total' => $taxTotal2,
                'grand_total' => $grandTotal2,
                'rounded_total' => $roundedTotal2,
                'amount_paid' => $amountPaid2,
                'balance_returned' => $balance2,
            ]);
        }
    }
}
