<?php

namespace App\Services;

use App\Models\Denomination;
use App\Models\DenominationTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Calculate and generate invoice
     */
    public function generateInvoice(int $customerId, array $items, float $amountPaid): Invoice
    {
        return DB::transaction(function () use ($customerId, $items, $amountPaid) {
            $subtotal = 0;
            $taxTotal = 0;
            $invoiceItems = [];

            // Calculate totals and validate stock
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];

                if ($product->stock < $quantity) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock}");
                }

                $unitPrice = $product->price;
                $purchasePrice = $unitPrice * $quantity;
                $taxPercent = $product->tax_percent;
                $taxAmount = ($purchasePrice * $taxPercent) / 100;
                $totalPrice = $purchasePrice + $taxAmount;

                $subtotal += $purchasePrice;
                $taxTotal += $taxAmount;

                $invoiceItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'purchase_price' => $purchasePrice,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'total' => $totalPrice,
                ];
            }

            $grandTotal = $subtotal + $taxTotal;
            $roundedTotal = floor($grandTotal);  // Round down
            $balanceReturned = $amountPaid - $roundedTotal;

            if ($balanceReturned < 0) {
                throw new \Exception("Amount paid is less than the bill amount");
            }

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $customerId,
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'rounded_total' => $roundedTotal,
                'amount_paid' => $amountPaid,
                'balance_returned' => $balanceReturned,
                'payment_mode' => 'cash',
                'purchase_date' => now(),
            ]);

            // Create invoice items
            foreach ($invoiceItems as $item) {
                $item['invoice_id'] = $invoice->id;
                InvoiceItem::create($item);

                // Deduct stock
                $product = Product::find($item['product_id']);
                $product->decrement('stock', $item['quantity']);
            }

            // Calculate and store denomination breakdown
            if ($balanceReturned > 0) {
                $this->processDenominationBreakdown($invoice, $balanceReturned);
            }

            return $invoice;
        });
    }

    /**
     * Process denomination breakdown for balance
     */
    private function processDenominationBreakdown(Invoice $invoice, float $balanceAmount): void
    {
        $remainingBalance = $balanceAmount;
        $denominations = Denomination::orderBy('value', 'desc')->get();

        foreach ($denominations as $denomination) {
            if ($remainingBalance >= $denomination->value && $denomination->available_count > 0) {
                $countNeeded = (int) floor($remainingBalance / $denomination->value);
                $countToUse = min($countNeeded, $denomination->available_count);

                if ($countToUse > 0) {
                    DenominationTransaction::create([
                        'invoice_id' => $invoice->id,
                        'denomination' => $denomination->value,
                        'count_used' => $countToUse,
                    ]);

                    $denomination->decrement('available_count', $countToUse);
                    $remainingBalance -= ($countToUse * $denomination->value);
                }
            }
        }
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $lastInvoice = Invoice::latest('id')->first();
        $nextNumber = ($lastInvoice?->id ?? 0) + 1;
        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT) . '-' . now()->format('YmdHis');
    }
}
