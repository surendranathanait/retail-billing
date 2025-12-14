<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'subtotal',
        'tax_total',
        'grand_total',
        'rounded_total',
        'amount_paid',
        'balance_returned',
        'payment_mode',
        'purchase_date'
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function denominationTransactions(): HasMany
    {
        return $this->hasMany(DenominationTransaction::class);
    }
}
